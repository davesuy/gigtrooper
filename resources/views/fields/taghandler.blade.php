<label>{{  ucwords($handle) }}</label>
<ul id="{{ $handle }}"></ul>

@if (!empty($options))
    @foreach ($options as $option)
        <input type="hidden" name="fields[{{ $handle }}][]" value="{{ $option }}" />
    @endforeach
@endif

@section('beforebody')
    @parent
    @include('js.taghandler')
@endsection