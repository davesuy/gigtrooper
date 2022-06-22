<label>{{  ucwords($title) }}</label>

<input type="text" {!! (isset($class)) ? "class='" . $class . "'" : '' !!} style="width: 120px; display: inline; margin-right: 5px;" name="fields[{{ $handle }}]"
        id="{{ $handle }}-datepicker" readonly value="{{ $value }}" /> <a class="button btn-mini"
        id="{{ $handle }}-datepicker-clear">clear</a>

@section('beforebody')
    @parent
    @include('js.datepicker')
@endsection
