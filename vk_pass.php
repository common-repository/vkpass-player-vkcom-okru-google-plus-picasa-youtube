<?php
/*
Plugin Name: VKPass Özel Player (Ücretsiz)
Plugin URI: http://vkpass.com
Description: VKPass player ile vk.com, ok.ru, google plus & picasa, vimeo, dailymation, youtube, izlesene, mynettv, myvideo.az vb sitelerdeki videoları size özel playerda oynatabilirsiniz. Ayrıntılı bilgi: http://vkpass.com/
Version: 1.11
Author: VKPass
Author URI: http://vkpass.com
License: GPL2
*/

define('VK_PASS_FILE', __FILE__);
define('VK_PASS_PATH', plugin_dir_path(__FILE__));

define ("PLUGIN_NAME", "VKPass Özel Player (Ücretsiz)");
define ("PLUGIN_NICK", "wp_vkpass");
define ("PLUGIN_VERSION", "1.11");
define ("PLUGIN_DB_VERSION", "1.11");
define ("PLUGIN_DIR_NAME", trim(basename(dirname(__FILE__), '/' )));
define ("PLUGIN_URL", plugin_dir_url(__FILE__)); // already has trailing slash
define ("PLUGIN_PATH", plugin_dir_path(__FILE__)); // already has trailing slash
define ("ACCESS_TO_USE_THIS_PLUGIN", 0) ;
define ("ACCESS_TO_MANAGE_THIS_PLUGIN", 10);

function vkp_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=vkp_list_options">VKPass Player</a>';
    array_push($links, $settings_link);
    return $links;
}

$main_domains = array("vkpass.com", "xyzpass.com");

$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin", 'vkp_settings_link', 995);

class vk_pass {

    protected $option_name = 'vkp_OPTION';
	
    protected $data = array(
        'vkp_TOKEN' => '',
        'vkp_TYPE' => '',
        'vkp_LANG' => '',
        'vkp_sifreleme' => '',
        'vkp_ebutton' => '',
        'vkp_MAIL' => '',
        'vkp_PASS' => '',
        'vkp_player_width' => '100%',
        'vkp_player_height' => '400px'
    );

    public function __construct() {

        // Admin sub-menu
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_page'));

        // Listen for the activate event
        register_activation_hook(VK_PASS_FILE, array($this, 'activate'));

        // Deactivation plugin
        register_deactivation_hook(VK_PASS_FILE, array($this, 'deactivate'));
    }

    public function activate() {
        update_option($this->option_name, $this->data);
    }

    public function deactivate() {
        delete_option($this->option_name);
    }
	
    public function init() {

        // When a URL like /todo is requested from the,
        // blog (the URL is customizable) we will directly
        // include the index.php file of the application and exit
        $result = get_option('vkp_OPTION');
        
    }

    // White list our options using the Settings API
    public function admin_init() {
        register_setting('vkp_list_options', $this->option_name, array($this, 'validate'));
    }

    // Add entry in the settings menu
    public function add_page() {
        add_menu_page('VKPass Player', 'VKPass Player', 'manage_options', 'vkp_list_options', array($this, 'options_do_page'), "");
    }

    // Print the menu page itself
    public function options_do_page() {
        $options = get_option($this->option_name);
        ?>
        <link rel="stylesheet" id="wp-fastest-cache-css" href="<?php echo plugin_dir_url(__FILE__); ?>style.css?v=021" type="text/css" media="all">
        
        <div class="wrap">
	        <form method="post" action="options.php">
	            <h2><?php echo __s("VKPass Ayarlar", "VKPass Options"); ?></h2><hr><br>
	            <div class="tabGroup">
		            <input checked="checked" type="radio" id="vkp-options" name="tabGroup1">
		            <label for="vkp-options"><?php echo __s("Genel Ayarlar", "General Settings"); ?></label>
		            <input type="radio" id="vkp-hashLink" name="tabGroup1">
		            <label for="vkp-hashLink"><?php echo __s("Link Şifreleme", "Hash Link"); ?></label>
					<div class="tab1">
						<?php settings_fields('vkp_list_options'); ?>
						
						<div class="vkpass_info"><?php echo __s("Playerı kişiselleştirmek için, sitemizden VKPass Paneline üye olarak token kodunu buraya girmeniz gerekmektedir.", "If you would like to personalize your Player, you have to register VKPass Panel in our website and write the token code here."); ?></div>
						
						<div class="questionCon">
							<div class="question"><?php echo __s("VKPass Butonu", "VKPass Button"); ?></div>
							<div class="inputCon"><input type="checkbox" name="<?php echo $this->option_name?>[vkp_ebutton]"<?php if($options['vkp_ebutton'] == "on") {echo ' checked="checked"';} ?> > <?php echo __s("Wordpress yazı düzenleyicisinde VKPass iframe butonunu göster.", "Activate VKPass iframe button in Wordpress post editor."); ?></div>
						</div>
						
						<div class="questionCon">
							<div class="question"><?php echo __s("Token Kodu", "Token Code"); ?></div>
							<div class="inputCon"><input type="text" name="<?php echo $this->option_name?>[vkp_TOKEN]" value="<?php echo $options['vkp_TOKEN']; ?>" /></div>
						</div>
						
						<div class="questionCon">
							<div class="question"><?php echo __s("Site Türü", "Site Type"); ?></div>
							<div class="inputCon">
								<select name="<?php echo $this->option_name?>[vkp_TYPE]">
									<option><?php echo __s("Site türünüzü seçin", "Select your site type"); ?></option>
									<option value="0" <?php echo ($options['vkp_TYPE'] == 0 ? "selected" : ""); ?>><?php echo __s("Genel İçerik", "General Content"); ?></option>
									<option value="1" <?php echo ($options['vkp_TYPE'] == 1 ? "selected" : ""); ?>><?php echo __s("Yetişkin İçerik", "Adult Content"); ?></option>
								</select>
							</div>
						</div>
						
						
						<div class="questionCon qsubmit">
			                <div class="submit">
			                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			                </div>
						</div>
					</div>
					<div class="tab2">
						<div class="vkpass_info"><?php echo __s("Kaynakların başkalarınca tespit edilmemesi için bu alanı aktif edebilirsiniz. VKPass Panele kayıt olduğunuz bilgileri yazın.", "If you dont like to anyone recognise your sources, you can activate this. Fill the form with your VKPass Panel informations."); ?></div>
						
						<div class="questionCon">
							<div class="question"><?php echo __s("Link Şifreleme", "Link Hash"); ?></div>
							<div class="inputCon"><input class="hasher_check" type="checkbox" name="<?php echo $this->option_name?>[vkp_sifreleme]"<?php if($options['vkp_sifreleme'] == "on") {echo ' checked="checked"';} ?> > <?php echo __s("Link şifreleme aktif edilsin mi?", "Would you like to activate link hasher?"); ?></div>
						</div>
						<div class="questionCon">
							<div class="question"><?php echo __s("VKPass Mail", "VKPass Mail"); ?></div>
							<div class="inputCon"><input type="text" name="<?php echo $this->option_name?>[vkp_MAIL]" value="<?php echo $options['vkp_MAIL']; ?>" /></div>
						<div class="questionCon">
						</div>
							<div class="question"><?php echo __s("VKPass Şifre", "VKPass Pass"); ?></div>
							<div class="inputCon"><input type="password" name="<?php echo $this->option_name?>[vkp_PASS]" value="<?php echo $options['vkp_PASS']; ?>" /></div>
						</div>
						
						
						<div class="questionCon qsubmit">
			                <div class="submit">
			                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			                </div>
						</div>
					</div>
				</div>
				<div class="omni_admin_sidebar">
					<div class="omni_admin_sidebar_section omni_select <?php if(intval($options['vkp_LANG']) == 0) echo "dikkat "; else echo "omni_hide"; ?>">
					<h3>Language Preference</h3>
					<ul>
				    	<li>
				    		<label>Please select your language</label>
				    	</li>
				    	<li>
				    		<label>Lütfen dil seçimi yapın</label>
				    	</li>
				    	
						<div class="questionCon">
							<div class="inputCon">
								<select style="width:158px" name="<?php echo $this->option_name?>[vkp_LANG]">
									<option>Select your language</option>
									<option value="1" <?php echo ($options['vkp_LANG'] == 1 ? "selected" : ""); ?>>Türkçe</option>
									<option value="2" <?php echo ($options['vkp_LANG'] == 2 ? "selected" : ""); ?>>English</option>
								</select>
								<input style="line-height:21px;height:26px" type="submit" class="button-primary" value=">" />
							</div>
						</div>
				  	</ul>
				  </div>
				</div>
				<div class="omni_admin_sidebar">
					<div class="omni_admin_sidebar_section">
					<h3><?php echo __s("Kişiselleştirme", "Personalize"); ?></h3>
					<ul>
				    	<li>
				    		<label><?php echo __s("Paneli ziyaret edin:", "Visit your panel:"); ?> <a target="_blank" href="http://vkpass.com/panel">vkpass.com</a></a>
				    	</li>
				    	<li>
				    		<label><?php echo __s("Panelde size verilen Token kodunu Genel Ayarlardan kaydedin.", "Save your token in here General Settings"); ?></label>
				    	</li>
				    	<li>
				    		<label><?php echo __s("Artık tüm kişiselleştirme işlemlerini VKPass Panelinizden yapabiliriniz! Tebrikler.", "Now you personalize your Player in VKPass Panel. Cheers!"); ?></label>
				    	</li>
				  	</ul>
				  </div>
				</div>
			</form>
        </div>
        <script>
	        var hasher_check = document.getElementsByClassName("hasher_check")[0];

			hasher_check.onclick = function() {
			    var hasher_infos = document.getElementsByClassName("hasher_infos");
			    if(hasher_check.checked == true)
			       for(var i = 0; i < hasher_infos.length; i++) hasher_infos[i].style.display = "table-row";
			    else for(var i = 0; i < hasher_infos.length; i++) hasher_infos[i].style.display = "none";
			}
			
			hasher_check.onclick();
			
			var omni_select = document.getElementsByClassName("omni_select")[0];
			omni_select = omni_select.getElementsByTagName("h3")[0];
			
			omni_select.onclick = function() {
				var parenter = omni_select.parentNode;
				if( (" " + parenter.className + " ").replace(/[\n\t]/g, " ").indexOf(" omni_hide ") > -1 ) {
					parenter.className = parenter.className.replace("omni_hide","");
				} else {
					parenter.className = parenter.className + "omni_hide";
				}
			};
        </script>
        <?php
    }

    public function validate($input) {

        $valid = array();
        $valid['vkp_TOKEN'] = sanitize_text_field($input['vkp_TOKEN']);
        $valid['vkp_TYPE'] = sanitize_text_field($input['vkp_TYPE']);
        $valid['vkp_LANG'] = sanitize_text_field($input['vkp_LANG']);
        $valid['vkp_MAIL'] = sanitize_text_field($input['vkp_MAIL']);
        $valid['vkp_PASS'] = sanitize_text_field($input['vkp_PASS']);
        $valid['vkp_sifreleme'] = sanitize_text_field($input['vkp_sifreleme']);
        $valid['vkp_ebutton'] = sanitize_text_field($input['vkp_ebutton']);
        $valid['vkp_player_width'] = sanitize_text_field($input['vkp_player_width']);
        $valid['vkp_player_height'] = sanitize_text_field($input['vkp_player_height']);
		
        return $valid;
    }
}


$result = get_option('vkp_OPTION');

if($result['vkp_sifreleme'] == "on") {

	$domains = array("vk.com", "ok.ru", "odnoklassniki.ru", "picasaweb.google.com", "plus.google.com", "myvideo.az");

    add_filter('the_content','add_postdata_to_content', 996);
    function add_postdata_to_content($text) {
	    global $post;
        global $domains;
        global $main_domains;
        
        $result = get_option('vkp_OPTION');
        $result_vkp_TOKEN = $result['vkp_TOKEN'] == "" ? 'cve0ejrnbrpq' : $result['vkp_TOKEN'];
        $TOKEN = $result_vkp_TOKEN;
        $MAIL = $result['vkp_MAIL'];
        $PASS = $result['vkp_PASS']; 
        $CONTENT = get_the_content($post->ID, ''); 
        
		$SRCS = explode("src='", $CONTENT);
		array_shift($SRCS);
		
		if(isset($result["vkp_TYPE"]))
			$main_domain = $main_domains[$result["vkp_TYPE"]];
		if(empty($main_domain)) $main_domain = $main_domains[0];
		
		if(sizeof($SRCS) > 0) {
			foreach($SRCS as $SRC) {
				$SRC = explode("'", $SRC);
				$SRC = $SRC[0];

				foreach($domains as $domain) {
					if(stripos($SRC, $domain)) {
						$NEW_SRC = htmlspecialchars_decode($SRC);
						$NEW_SRC = urlencode($NEW_SRC);
						$NEW_SRC = @file_get_contents("http://{$main_domain}/token/{$TOKEN}/hashlink?mail={$MAIL}&pass={$PASS}&link={$NEW_SRC}");
						$CONTENT = str_replace($SRC, $NEW_SRC, $CONTENT);
						$CONTENT = str_replace("src=", "allowfullscreen src=", $CONTENT);
					}
				}
			}
		}
		
		$SRCS = explode('src="', $CONTENT);
		array_shift($SRCS);
		if(sizeof($SRCS) > 0) {
			foreach($SRCS as $SRC) {
				$SRC = explode('"', $SRC);
				$SRC = $SRC[0];

				foreach($domains as $domain) {
					if(stripos($SRC, $domain)) {
						$NEW_SRC = htmlspecialchars_decode($SRC);
						$NEW_SRC = urlencode($NEW_SRC);
						$NEW_SRC = @file_get_contents("http://{$main_domain}/token/{$TOKEN}/hashlink?mail={$MAIL}&pass={$PASS}&link={$NEW_SRC}");
						$CONTENT = str_replace($SRC, $NEW_SRC, $CONTENT);
						$CONTENT = str_replace("src=", "allowfullscreen src=", $CONTENT);
					}
				}
			}
		}
        
        return $CONTENT;
    }

}

if($result["vkp_ebutton"] == "on") {
	add_filter('mce_buttons', 'myplugin_register_buttons', 997);
	
	function myplugin_register_buttons($buttons) {
	   array_push($buttons, 'separator', 'vkpass');
	   return $buttons;
	}
	
	add_filter('mce_external_plugins', 'myplugin_register_tinymce_javascript', 998);
	
	function myplugin_register_tinymce_javascript($plugin_array) {
	   $plugin_array['vkpass'] = plugins_url('/tinymce-plugin.js',__file__);
	   return $plugin_array;
	}
	
	function override_tinymce_option($initArray) {
	    $opts = 'iframe[*]';
	    $initArray['extended_valid_elements'] = $opts;
	    return $initArray;
	}
	add_filter('tiny_mce_before_init', 'override_tinymce_option', 999);
}

add_action('wp_head', 'vkp_head');
function vkp_head () {
	global $main_domains;
        
    $result = get_option('vkp_OPTION');
    $result_vkp_TOKEN = $result['vkp_TOKEN'] == "" ? 'cve0ejrnbrpq' : $result['vkp_TOKEN'];
    $main_domain = $main_domains[$result['vkp_TYPE']];
    if(empty($main_domain)) $main_domain = $main_domains[0];
    
    echo '<script>
  !function(d, h, s, id) { 
    var js, fjs = d.getElementsByTagName(h)[0];
    if(!d.getElementById(id)) {
      js = d.createElement(s);
      js.id = id;
      js.src = "http://'.$main_domain.'/configure/'.$result_vkp_TOKEN.'.js";
      fjs.appendChild(js,fjs);
    }
  } (document, "head", "script", "vkpass-configure");
</script>';
}

function __s($tr, $en) {
	$result = get_option('vkp_OPTION');
	if($result["vkp_LANG"] == 1) return $tr;
	else return $en;
}

new vk_pass();
?>
