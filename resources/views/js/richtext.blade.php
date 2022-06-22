<script type="text/javascript">

    //CKEDITOR.config.simpleImageBrowserURL = "/richtextimages/?dir={{ $dir }}";
    /**
     * Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
     * For licensing, see LICENSE.md or http://ckeditor.com/license
     */

    /* exported initSample */

    if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
        CKEDITOR.tools.enableHtml5Elements(document);
    }

    // The trick to keep the editor in the sample quite small
    // unless user specified own height.
    {{--CKEDITOR.config.height = {{ $height }};--}}
        CKEDITOR.config.width = 'auto';

    var initSample = (function() {

        var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

        return function() {
            var editorElement = CKEDITOR.document.getById('{{ $handle }}');

            // :(((
            if (isBBCodeBuiltIn) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=http://ckeditor.com]CKEditor[/url].'
                );
            }

            // Depending on the wysiwygare plugin availability initialize classic or inline editor.
            if (wysiwygareaAvailable) {
                CKEDITOR.replace('{{ $handle }}', {
                    customConfig: '/ckeditor/full.js',
                    "extraPlugins": 'justify,imagebrowser,embed,autoembed,pastecode',
                    "imageBrowser_listUrl": "/richtextimages/?dir={{ $dir }}",
                    embed_provider: '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}',
                    height: {{ $height }}
                });
            } else {
                editorElement.setAttribute('contenteditable', 'true');
                CKEDITOR.inline('{{ $handle }}');

                // TODO we can consider displaying some info box that
                // without wysiwygarea the classic editor may not work.
            }
        };

        function isWysiwygareaAvailable() {
            // If in development mode, then the wysiwygarea must be available.
            // Split REV into two strings so builder does not replace it :D.
            if (CKEDITOR.revision == ('%RE' + 'V%')) {
                return true;
            }

            return !!CKEDITOR.plugins.get('wysiwygarea');
        }
    })();

    @if ($basic == true)
    CKEDITOR.replace({{ $handle }}, {
        customConfig: '/ckeditor/minimal_config.js',
        height: {{ $height }},
        extraPlugins: 'wordcount,notification',
        embed_provider: '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}',
        wordcount: {

            // Whether or not you want to show the Word Count
            showWordCount: false,

            // Whether or not you want to show the Char Count
            showCharCount: true,

            // Maximum allowed Word Count
            maxWordCount: -1,
            @if ($maxChar)
            // Maximum allowed Char Count
            maxCharCount: {{ $maxChar }}
            @endif
        }
    });
    @else
    initSample();
    @endif
    CKEDITOR.on('dialogDefinition', function (ev) {
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        var dialog = dialogDefinition.dialog;
        var editor = ev.editor;

        if (dialogName == 'image') {
            dialogDefinition.onOk = function (e) {
                var widthField = dialog.getValueOf( 'info', 'txtWidth' ),
                    heightField = dialog.getValueOf( 'info', 'txtHeight' );

                var imageSrcUrl = e.sender.originalElement.$.src;
                var width = e.sender.originalElement.$.width;
                var height = e.sender.originalElement.$.height;
                var imgHtml = CKEDITOR.dom.element.createFromHtml('<img src="' + imageSrcUrl + '" layout="responsive" width="' + widthField + '" height="' + heightField + '" alt="" />');
                editor.insertElement(imgHtml);
            };
        }
    });
{{--    CKEDITOR.on('instanceReady', function (ev) {
        var editor = ev.editor,
            dataProcessor = editor.dataProcessor,
            htmlFilter = dataProcessor && dataProcessor.htmlFilter;

        htmlFilter.addRules( {
            elements : {
                $ : function( element ) {
                    // Output dimensions of images as width and height attributes on src
                    if ( element.name == 'img' ) {
                        var style = element.attributes.style;
                        if (style) {
                            // Get the width from the style.
                            var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec( style ),
                                width = match && match[1];

                            // Get the height from the style.
                            match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec( style );
                            var height = match && match[1];

                            var imgsrc = element.attributes.src + "?width=" + width + "&height=" + height;

                           // element.attributes.src = imgsrc;
                           // element.attributes['data-cke-saved-src'] = imgsrc;
                        }
                    }
                }
            }
        });
    });--}}
</script>
