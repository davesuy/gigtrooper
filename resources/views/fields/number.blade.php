<label>{{ ucwords($title) }}</label>
<?php $disabled = (!empty($params) && isset($params['disabled']) && $params['disabled'] == true && $params['model']
  != null) ? 'readonly' : ''  ?>
<input  {{ $disabled }} type="number" class="input-text full-width" name="fields[{{ $name }}]" value="{{ $value }}" />

@php
$fieldName = 'fields.' . $name;
@endphp

@if ($errors->has($fieldName))
  <span class="help-block">
    <strong>{{ $errors->first($fieldName) }}</strong>
  </span>
@endif