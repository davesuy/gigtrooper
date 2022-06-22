@if (!empty($options))
    @php
        $rise = 1;
     @endphp
    <label>{{  ucwords($name) }}</label>
    <div class="row">
        @foreach ($options as $option)
            @php $selected = ($option->selected == true) ? 'checked' : '' @endphp
            <div class="col-sm-3">
                    <div class="checkbox">
                    <label>
                    <input type="checkbox"
                            name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}][]" {!! $selected  !!}
                            value="{{ $option->value }}" />
                    {{ $option->label  }}
                    </label>
                    </div>
                @php
                    if (($rise % 4) == 0)
                    {
                        echo "</div><div class='row'>";
                    }
                    $rise++;
                @endphp
            </div>
        @endforeach
    </div>
@endif
