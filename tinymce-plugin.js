tinymce.PluginManager.add('vkpass', function(editor, url) {
    // Add a button that opens a window
    editor.addButton('vkpass', {
        text: 'VKP',
        icon: false,
        onclick: function() {
            // Open window
            editor.windowManager.open({
                title: 'Kaynak Video Ekle',
                body: [
                    {type: 'textbox', name: 'title', label: 'Kaynak Linki', style: 'width:200px'},
                    {type: 'textbox', name: 'tr_sub', label: 'Altyazı Linki (Türkçe)', style: 'width:200px'},
                    {type: 'textbox', name: 'en_sub', label: 'Altyazı Linki (İngilizce)', style: 'width:200px'},
                    {type: 'label', name: '', label: 'Altyazınız yoksa kutuları boş bırakınız.'}
                ],
                onsubmit: function(e) {
                    // Insert content when the window form is submitted
                    
                    var subtitles = "", i = 1;
                    
                    if(e.data.tr_sub != "") {
						subtitles += 'c' + i + '_label="Türkçe" c' + i + '_file="' + e.data.tr_sub + '"';
						i++;
					}
					
                    if(e.data.en_sub != "") {
						subtitles += 'c' + i + '_label="İngilizce" c' + i + '_file="' + e.data.en_sub + '"';
						i++;
					}
                    
                    editor.insertContent('<iframe width="100%" height="400px" allowfullscreen src="' + e.data.title + '" ' + subtitles + '></iframe>');
                }
            });
        }
    });
});