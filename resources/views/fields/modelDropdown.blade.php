@if (!empty($options))
    <label>{{ $title }}</label>
    <select class="dropdown-{{ $name }}" name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}]">
        @foreach ($options as $option)
            @php $selected = ($option->selected == true) ? 'selected="selected"' : '' @endphp
            <option {!! $selected  !!} value="{{ $option->value }}"> {{ $option->label  }}</option>
        @endforeach
    </select>
@endif


@section('beforebody')
    @parent

    @if ($subfield)
        <script type="text/javascript">
            jQuery(function($) {
                var CSRF_TOKEN = $("input[name=_token]").attr('value');

                $(".dropdown-{{ $name }}").change(function() {
                    var value = $(this).val();

                    var data = {
                        id: value, _token: CSRF_TOKEN, modelName: '{{ $modelName }}'
                        {!! ($modelId)? ", elementId:" . $modelId : '' !!}};
                    if ($("{{ $subfield }}").length) {
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
                });
            });

        </script>
    @endif
@endsection
