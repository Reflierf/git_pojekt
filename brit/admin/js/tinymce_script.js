
$(document).ready(function(){
    tinymce.init({
        selector:'textarea#acontent',
        height:450,
        language: "hu_HU",
        menubar: true,
        menubar: 'file edit insert view format table tools',
        statusbar: false,
        entity_encoding: 'raw',
        plugins: "link image hr textcolor preview code emoticons charmap table insertdatetime contextmenu print",
        contextmenu: "link image | preview code print",
        toolbar1: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright '+
        'alignjustify alignnone | link image | hr | emoticons charmap | table | insertdatetime | print',
        toolbar2: 'formatselect fontselect fontsizeselect | outdent indent blockquote | forecolor backcolor | preview code | removeformat',
        fontsize_formats: '6pt 8pt 10pt 12pt 14pt 18pt 20pt 24pt 30pt 36pt',
        link_list: [
            {title: 'TessaFowler.com', value: 'http://www.tessafowler.com/'},
            {title: 'Bibi Jones filmek', value: 'http://www.cduniverse.com/sresult.asp?HT_Search=ALL&HT_Search_Info=BiBi+Jones&style=ice'},
            {title: 'Serena', value: 'http://www.cosmid.net/models_page.html?model=serena'},
        ],
        default_link_target: "_blank",
        link_context_toolbar: true,
        target_list: [
            {title: 'Ugyanott', value: '_self'},
            {title: 'Új lapon', value: '_blank'},
            {title: 'LIghtbox', value: '_lightbox'},
        ],
        image_list: [
            {title: 'Brittanys Bod', value: 'http://localhost/brit/img/0097.jpg'},
            {title: 'Alana Soares', value: 'http://localhost/brit/img/alana145.JPG'}
        ],
        image_advtab: true,
        relative_urls : false,
        remove_script_host : false,
        convert_urls : false,
    }) 
});