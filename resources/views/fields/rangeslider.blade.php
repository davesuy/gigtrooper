<div class="text-center">
	@if (!empty($title))
		<label for="amount">{{  ucwords($title) }}</label>
		@endif
	<p>
	<input style="display: inline; width: 170px"
			type="text"
			name="{{ (isset($key)) ? $key : 'fields' }}[{{ $handle }}]"
			id="{{ $handle }}-amount"
			readonly />
	</p>
</div>
<div id="{{ $handle }}-range"></div>
@section('beforebody')
    @parent

    <script type="text/javascript">
    jQuery( function($) {
	    jQuery( "#{{ $handle }}-range" ).slider({
		    range: true,
		    min: 0,
	  		@if (!empty($step))
				step: {{ $step }},
			@endif
		    max: 150000,
		    values: [ {{ $value['min'] }}, {{ $value['max'] }} ],
		    slide: function( event, ui ) {
		    	if (ui.values[ 0 ] == 0 &&  ui.values[ 1 ] == 0)
				{
					jQuery( "#{{ $handle }}-amount" ).val('{{ $message }}');
				}
				else
				{
				  jQuery( "#{{ $handle }}-amount" ).val(
				  ui.values[ 0 ]
				  //ui.values[ 0 ].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
				  + " - " +
				  ui.values[ 1 ]
				  //ui.values[ 1 ].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
				  );
				}
		    }
	    });
	    jQuery("#{{ $handle }}-amount").val(
			@if ($value['min'] == 0 && $value['max'] == 0)
		'{{ $message }}'
			@else
		    	$( "#{{ $handle }}-range" ).slider( "values", 0 ) + " - " + $( "#{{ $handle }}-range" ).slider("values", 1 )
			@endif
		);
    } );
    </script>

@endsection