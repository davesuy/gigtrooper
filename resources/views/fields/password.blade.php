
<label>{{  ucwords($title) }}</label>
<?php $disabled = (!empty($params) && isset($params['disabled']) && $params['disabled'] == true && $params['model']
  != null) ? 'readonly' : ''  ?>
<input  {{ $disabled }} type="password" class="input-text full-width" name="fields[{{ $name }}]" value="" />
<div>&nbsp;</div>
<label>Confirm {{  ucwords($name) }}</label>
<input  {{ $disabled }} type="password" class="input-text full-width" name="{{ $name }}_confirm" value="" />

@php
$fieldName = 'fields.' . $name;
@endphp

@if ($errors->has($fieldName))
  <span class="help-block">
    <strong>{{ $errors->first($fieldName) }}</strong>
  </span>
@endif