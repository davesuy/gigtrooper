<label>{{ $title }}</label>
<?php $disabled = (!empty($params) && isset($params['disabled']) && $params['disabled'] == true && $params['model']
  != null) ? 'readonly' : ''  ?>
<input  {{ $disabled }}
        type="text" class="input-text full-width"
        name="{{ (isset($key)) ? $key : 'fields' }}[{{ $name }}]"
        value="{{ $value }}" />

@php
$fieldName = 'fields.' . $name;
@endphp

@if ($errors->has($fieldName))
  <span class="help-block">
    <strong>{{ $errors->first($fieldName) }}</strong>
  </span>
@endif