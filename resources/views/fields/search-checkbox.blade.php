@if (!empty($options))
    <label><strong>{{  ucwords($title) }}</strong>
        @if ($all)
            : Showing All
        @endif
    </label>


    @foreach ($options as $option)
        @php $selected = ($option->selected == true) ? 'checked' : '' @endphp
            <label>
            <input type="checkbox"
                    name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}][]" {!! $selected  !!}
                    value="{{ $option->value }}" />
            {{ $option->label  }}
            </label>
    @endforeach
        {{-- This is needed to remove query string for empty checkbox group --}}
        <input type="hidden"
                name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}][x]"
                value="1" />
@endif