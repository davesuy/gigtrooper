<label>{{  ucwords($title) }}</label>

<input type="hidden" id="{{ $handle }}-datepicker" value="{{ $inputValue['value'] }}" />
<input type="text" id="{{ $handle }}-datepicker-alt" value="{{ $inputValue['alt'] }}" />

<input type="hidden" name="filters[{{ $handle }}][day]" id="{{ $handle }}-datepicker-day"
        value="{{ $dateValues['day'] }}" />
<input type="hidden" name="filters[{{ $handle }}][month]" id="{{ $handle }}-datepicker-month"
        value="{{ $dateValues['month'] }}" />
<input type="hidden" name="filters[{{ $handle }}][year]" id="{{ $handle }}-datepicker-year"
        value="{{ $dateValues['year'] }}" />

@section('beforebody')
    @parent
    <script>
        jQuery( function() {
            jQuery( "#{{ $handle }}-datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "d M yy",
	            altField: "#{{ $handle }}-datepicker-alt",
	            altFormat: "{{ $dateFormat }}",
	            showOn: "button",
	            buttonImage: "/images/calendar.gif",
	            buttonImageOnly: true,
	            onClose: function(dateText, inst) {
                  jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                    @if (empty($settings['hideDay']) == true)

                    @endif
                     @if (empty($settings['hideMonth']) == true)

                    @endif
                     @if (empty($settings['hideYear']) == true)

                     @endif

                     jQuery("#{{ $handle }}-datepicker-day").val(inst.selectedDay);
                     jQuery("#{{ $handle }}-datepicker-month").val(inst.selectedMonth + 1);
                     jQuery("#{{ $handle }}-datepicker-year").val(inst.selectedYear);
	            },
	            beforeShow: function(input, inst) {
			        jQuery("#ui-datepicker-div").removeClass("{{ $handle }}-datepicker");
			        jQuery("#ui-datepicker-div").addClass("{{ $handle }}-datepicker");
	            }
            });


        } );
    </script>
    <style>
    @if (!empty($settings['hideDay']) == true)
        .{{ $handle }}-datepicker .ui-datepicker-calendar {
            display: none;
        }
    @endif

    @if (!empty($settings['hideMonth']) == true)
        .{{ $handle }}-datepicker .ui-datepicker-month {
            display: none;
        }
    @endif

    @if (!empty($settings['hideYear']) == true)
        .{{ $handle }}-datepicker .ui-datepicker-year {
            display: none;
        }
    @endif
    </style>
@endsection

