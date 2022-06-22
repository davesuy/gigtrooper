@if (!empty($options))
    <div class="form-group">
        <select class="full-width search-dropdown ui fluid search" name="homeSearch[memberCategory]">
            <option value="">Enter a service or a talent...</option>
            @foreach ($options as $option)
                @php $selected = ($option->selected == true) ? 'selected="selected"' : '' @endphp
                <option {!! $selected  !!} value="{{ $option->value }}"> {{ $option->label  }}</option>
            @endforeach
        </select>
    </div>
@endif
