<label>{{  ucwords($name) }}</label>
<?php $disabled = (!empty($params) && isset($params['disabled']) && $params['disabled'] == true && $params['model']
  != null) ? 'readonly' : ''  ?>
<input  {{ $disabled }} type="text" class="input-text full-width" name="fields[{{ $name }}]" value="
{{ ($value)? $value : false }}" />

@php
$fieldName = 'fields.' . $name;
@endphp

@if ($errors->has($fieldName))
  <span class="help-block">
    <strong>{{ $errors->first($fieldName) }}</strong>
  </span>
@endif