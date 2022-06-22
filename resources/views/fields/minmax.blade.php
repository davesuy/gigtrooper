<div class="text-center">
	@if (!empty($title))
		<label for="amount">{{  ucwords($title) }}</label>
		@endif
	<p>
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][min]"
		id="{{ $handle }}-amount" placeholder="Minimum" step="100" min="0" value="{{ $value['min'] }}" />
	-
	<input class="minmax-input"
		type="number"
		name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}][max]"
		id="{{ $handle }}-amount" placeholder="Maximum" step="100" min="0" value="{{ $value['max'] }}" />

	</p>
</div>