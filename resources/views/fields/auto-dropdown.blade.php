@if (!empty($options))

        {!! (!empty($title))? "<label><strong>$title</strong></label>" : '' !!}
        <select class="dropdown-{{ $name }}{{ (isset($class)) ? ' ' . $class : '' }} full-width search-dropdown ui fluid search" name="{{ (isset($key)) ? $key :
        'fields' }}[{{ $name }}]">
            @foreach ($options as $option)
                @php $selected = ($option->selected == true) ? 'selected="selected"' : '' @endphp
                <option {!! $selected  !!} value="{{ $option->value }}"> {{ $option->label  }}</option>
            @endforeach
        </select>

@endif
@section('header')
    @parent
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet"/>
@endsection


@section('beforebody')
    @parent
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript">

        jQuery(function($) {
            var CSRF_TOKEN = $("input[name=_token]").attr('value');

            $('.search-dropdown').dropdown({
                allowAdditions: true,
                onChange: function(value, text, $selectedItem) {

                    $formGroup = $(this).parents('.form-group');
                    if (value != "") {
                        $formGroup.find('.with-errors').html('');
                    }
                }
            })
        });
    </script>
@endsection