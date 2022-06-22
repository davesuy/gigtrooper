<div class="text-center">
	@if (!empty($title))
		<label for="amount">{{  ucwords($title) }}</label>
		@endif
	<p class="text-center">
	<strong>Minimum:</strong><br />
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][min][low]"
		id="{{ $handle }}-amount" placeholder="lowest" step="100" min="0"
			value="{{ (!empty($value['min']['low']))? $value['min']['low'] : "" }}" />
	-
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][min][high]"
		id="{{ $handle }}-amount" placeholder="highest" step="100" min="0"
			value="{{ (!empty($value['min']['high']))? $value['min']['high'] : "" }}" />
	</p>

	<p class="text-center">
	<strong>Maximum:</strong><br />
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][max][low]"
		id="{{ $handle }}-amount" placeholder="lowest" step="100" min="0"
			value="{{ (!empty($value['max']['low']))? $value['max']['low'] : "" }}" />
	-
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][max][high]"
		id="{{ $handle }}-amount" placeholder="highest" step="100" min="0"
			value="{{ (!empty($value['max']['high']))? $value['max']['high'] : "" }}" />
	</p>

</div>