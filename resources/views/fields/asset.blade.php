<label>{{  ucwords($title) }}</label>

<div id="fileupload-{{ $handle }}" class="fileupload-form">
    <div action="" method="POST"
            enctype="multipart/form-data">
    <input id="fileupload-token-{{ $handle }}" type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input id="fileupload-handle-{{ $handle }}" type="hidden" name="fileuploadHandle" value="{{ $handle }}" />
    <input id="fileupload-handle-id-{{ $handle }}" type="hidden" name="fileuploadId" value="{{ $id }}" />
    <input id="fileupload-handle-label-{{ $handle }}" type="hidden" name="fileuploadLabel" value="{{ $label }}" />
    <!-- Redirect browsers with JavaScript disabled to the origin page -->
    <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">
        <div class="col-sm-7">
            <small>Upload limit is 2MB each.</small><br/>
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button fileinput-button-{{ $handle }}">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add</span>
                    <input type="file" name="files[]" multiple>
                </span>
            <button type="submit" class="btn btn-xs btn-primary start fileinput-button-{{ $handle }}">
                <i class="glyphicon glyphicon-circle-arrow-up"></i>
                <span>Upload</span>
            </button>
            <button type="reset" class="btn btn-xs btn-warning cancel fileinput-button-{{ $handle }}">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>Cancel</span>
            </button>
            @if ($limit != 1)
            <button type="button" class="btn btn-xs btn-danger delete">
                <i class="glyphicon glyphicon-trash"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" class="toggle">
            @endif
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
        </div>
        <!-- The global progress state -->
        {{-- xxtempxx hide for amazon s3 --}}
        <div class="col-sm-5 fileupload-progress fade hide">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </div>
</div>
@section('beforebody')
<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

    <!-- The template to display files available for upload -->
<script id="template-upload-{{ $handle }}" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="start" disabled>Start</button>
            {% } %}
            {% if (!i) { %}
                <button class="cancel">Cancel</button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download-{{ $handle }}" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
            <input type="hidden" name="fileupload[]" value="{%=file.url%}" />
        </td>
        <td>
            <p class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="error">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="delete" data-type="{%=file.deleteType%}"
            data-url="{%=file.deleteUrl%}&_token={{ csrf_token() }}&fileuploadHandle={{ $handle }}&fileuploadId={{ $id
             }}&fileuploadLabel={{ $label }}"{% if
            (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Delete</button>
            @if ($limit != 1)
            <input type="checkbox" name="delete" value="1" class="toggle">
            @endif
        </td>
    </tr>
{% } %}
</script>

    @parent
    @include('js.fileupload')
@endsection
