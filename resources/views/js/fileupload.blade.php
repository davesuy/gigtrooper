<script type="text/javascript">

    jQuery(document).ready(function ($) {
        'use strict';

        var formToken        = jQuery("#fileupload-token-{{ $handle }}").val();
        var fileuploadHandle = jQuery("#fileupload-handle-{{ $handle }}").val();
        var fileuploadId     = jQuery("#fileupload-handle-id-{{ $handle }}").val();
        var fileuploadLabel  = jQuery("#fileupload-handle-label-{{ $handle }}").val();

        var fileLimit = {{ $limit }};
	    $('.start.fileinput-button-{{ $handle }}').hide();
        // Initialize the jQuery File Upload widget:
        $('#fileupload-{{ $handle }}').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},

            url: '/fileupload',
	        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
	        maxFileSize: 2000000, // 2MB
            uploadTemplateId: 'template-upload-{{ $handle }}',
            downloadTemplateId: 'template-download-{{ $handle }}',
            formData: { _token : formToken, fileuploadHandle: fileuploadHandle, fileuploadId: fileuploadId,
                fileuploadLabel: fileuploadLabel },
	        maxNumberOfFiles: fileLimit,
            submit: function(e, data) {

            	var tplFiles = $(this).find('.template-download').length + 1;
                if (tplFiles >= fileLimit)
                {
	                $('.fileinput-button-{{ $handle }}').hide();
                }

            },
	        destroyed: function() {
                var tplFiles = $(this).find('.template-download').length;

			    if (tplFiles < fileLimit)
                {
	                $('.fileinput-button-{{ $handle }}').show();
                }
           }
        }).bind('fileuploadadd', function (e, data) {
	        $('.start.fileinput-button-{{ $handle }}').show();
        }).on('fileuploadprogress', function (e, data) {
	        // This is what makes everything really cool, thanks to that callback
	        // you can now update the progress bar based on the upload progress.
	        var percent = Math.round((data.loaded / data.total) * 100);
//
//	        var file = data.files[0];
//	        console.log(file);
            console.log(percent);
	        $('.progress')
	        .attr('aria-valuenow', percent)
	        .children().first().css('width', percent + '%');
        } );

        // Enable iframe cross-domain access via redirect option:
        $('#fileupload-{{ $handle }}').fileupload(
                'option',
                'redirect',
                window.location.href.replace(
                        /\/[^\/]*$/,
                        '/cors/result.html?%s'
                )
        );

        // Load existing files:
        $('#fileupload-{{ $handle }}').addClass('fileupload-processing');

        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload-{{ $handle }}').fileupload('option', 'url'),
            dataType: 'json',
            data: { _token : formToken, fileuploadHandle: fileuploadHandle, fileuploadId: fileuploadId,
                fileuploadLabel: fileuploadLabel },
            context: $('#fileupload-{{ $handle }}')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
	        $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
        });


    });


</script>
