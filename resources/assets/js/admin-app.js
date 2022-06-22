jQuery(document).ready(function($) {
	$("#sortByDrop").change(function(){
		$form = $(this).parents().find("#sortForm");
		$form.submit();
	})
	//
	//$("#filterByDrop").change(function(){
	//	$form = $(this).parents().find("#filterForm");
	//	$form.submit();
	//})

	$("#elementSelectAll").click(function() {
		$(".elementCheckbox").prop('checked', $(this).prop('checked'));
	});
})

