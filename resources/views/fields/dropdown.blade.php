@if (!empty($options))

        {!! (!empty($title))? "<label><strong>$title</strong></label>" : '' !!}
        <select class="dropdown-{{ $name }}{{ (isset($class)) ? ' ' . $class : '' }}" name="{{ (isset($key)) ? $key :
        'fields' }}[{{ $name }}]">
            @foreach ($options as $option)
                @php $selected = ($option->selected == true) ? 'selected="selected"' : '' @endphp
                <option {!! $selected  !!} value="{{ $option->value }}"> {{ $option->label  }}</option>
            @endforeach
        </select>

@endif
