{{--@if (!empty($categories))--}}
    {{--@foreach($categories as $category)--}}
        {{--<table>--}}
            {{--<tr>--}}
                {{--@php $selected = (in_array($category->id, $requestValues)) ? 'checked' : '' @endphp--}}
                {{--<td>--}}
                    {{--<div class="checkbox">--}}
                    {{--<input type="checkbox" name="filters[{{ $handle }}][]"--}}
                            {{--class="categoryelect elementCheckbox no-space"--}}
                            {{--value="{{ $category->id }}" />--}}
                    {{--</div>--}}
                {{--</td>--}}
                {{--<td><a href="/{!! \Config::get('app.cp') !!}/{{ $url }}/{{ $category->id  }}/edit"> {{--}}
                            {{--$category->title }}</a></td>--}}
            {{--</tr>--}}
        {{--</table>--}}
    {{--@endforeach--}}
{{--@endif--}}
<div style="background:#f5f5f5; padding: 20px 10px">
<label>{{ $title }}</label>

{!! $menuTree !!}
</div>