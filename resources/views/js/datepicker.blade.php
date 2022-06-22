<script>
    jQuery(function() {

        var $dates = jQuery("#{{ $handle }}-datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "d-M-yy",
            showOn: "both",
            buttonImage: "/images/calendar.gif",
            buttonImageOnly: true,
            buttonText: "Select date",
            @if (isset($settings['minDate']))
                minDate: {{ $settings['minDate'] }}
            @endif
        }).on( "change", function() {
            var value = jQuery(this).val();

            if (value !== '') {
                jQuery(this).parents('.form-group').find('.with-errors').html('');
            }
        });

        jQuery('#{{ $handle }}-datepicker-clear').on('click', function() {
            $dates.datepicker('setDate', null);
        });
    });

</script>