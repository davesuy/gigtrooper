{{--taghandler--}}
<script type="text/javascript">
    jQuery(document).ready(function($) {

      var handle = "{{ $handle }}";
      $tagHandler = $("#" + handle);
      $tagHandler.tagHandler({
	   getData: { label: '{{ $label }}', id: '{{ $id }}', name: '{{ $handle }}', property: '{{ $property }}' },
	   getURL: '/taghandler',
	   autocomplete: true,
	   allowAdd: true,
	   initLoad: true,
	   minChars: 2,
       onAdd: function(tag) {
           $("<input type='hidden' name='fields[" + handle + "][]' value='"+tag+"' />").insertAfter($tagHandler.parent());
           return 1;
       },
        onDelete: function(tag) {
            $tag = $tagHandler.parents('.field').find('input[type="hidden"][value="'+tag+'"]');
            if ($tag.length > 0)
            {
                $tag.remove();
            }

            return 1;
       }
      });
    })
</script>
