<div class="sortBy">
    @if (!empty($sorts))
        <select name="sortBy[]" class="left">
            @foreach($sorts as $sortk => $sortv)
				@php $selected = ($sortv['value'] == $sortValue) ? "selected='selected'" : "" @endphp
                <option {!! $selected !!} value="{{ $sortv['value'] }}" class="{{ $sortk }}">{{
                                         $sortv['label'] }}</option>
            @endforeach
        </select>
    @endif

</div>