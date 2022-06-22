@if (!empty($options))
    @php
        $rise = 1;
     @endphp
    <label><strong>{{ $title }}</strong></label>

    @foreach ($options as $option)
        @php $selected = ($option->selected == true) ? 'checked' : '' @endphp
        <div class="col-sm-3">

                <label>
                <input type="radio"
                        name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}]" {!! $selected  !!}
                        value="{{ $option->value }}" />
                {{ $option->label  }}
                </label>

            @php
                if (($rise % 4) == 0)
                {
                    echo "</div><div class='row'>";
                }
                $rise++;
            @endphp
        </div>
    @endforeach
    <div class="clearfix"></div>
@endif
