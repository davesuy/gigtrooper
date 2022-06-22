<label>{{  ucwords($title) }}</label>
<?php $disabled = (!empty($params) && isset($params['disabled']) && $params['disabled'] == true && $params['model'] != null) ? 'disabled' : ''  ?>

<textarea id="{{ $handle }}" name="fields[{{ $handle }}]">
   {{ $value }}
</textarea>

@section('beforebody')
   @parent
   @include('js.richtext')
@endsection
