@if ($title)
    <label>{{ $title }}</label>
@endif
<div class="ui dropdown button {{ $name }}">
    <span class="default text">{!! $text !!}</span>
    <input type="hidden" name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}]"
           value="{{ $idValue }}">
    <i class="dropdown icon"></i>
    {!! $categoryDisplay !!}
</div>

@section('beforebody')
    @parent

    <script type="text/javascript">
        jQuery(function($) {
            var CSRF_TOKEN = $("input[name=_token]").attr('value');
            $categoryDropdown = $('.ui.dropdown.{{ $name }}').dropdown({
                allowCategorySelection: true,
                preserveHTML: true,
                selector: {
                    menuIcon: '.pull-right.glyphicon.glyphicon-chevron-right'
                },
                @if ($subfield)
                onChange: function(value, text, $choice) {
                    if ($("{{ $subfield }}").length) {
                        var data = {
                            id: value, _token: CSRF_TOKEN, modelName: '{{ $modelName }}'
                            {!! ($modelId)? ', elementId:' . $modelId : '' !!}};
                        $("{{ $subfield }}").find('.ajax.loading').removeClass('hide');

                        $.ajax({
                            url: '/profile/subfields',
                            type: 'POST',
                            data: data,
                            success: function(data) {
                                $("{{ $subfield }}").find('.ajax.loading').addClass('hide');
                                $("{{ $subfield }} .content").html(data);
                            }
                        });
                    }
                }
                @endif
            });
        });
    </script>
@endsection
