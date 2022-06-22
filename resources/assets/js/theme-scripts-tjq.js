tjq(document).ready(function() {
	tjq("#profile .edit-profile-btn").click(function(e) {
		e.preventDefault();
		tjq(".view-profile").fadeOut();
		tjq(".edit-profile").fadeIn();
	});

	setTimeout(function() {
		tjq(".notification-area").append('<div class="info-box block"><span class="close"></span><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus ab quis a dolorem, placeat eos doloribus esse repellendus quasi libero illum dolore. Esse minima voluptas magni impedit, iusto, obcaecati dignissimos.</p></div>');
	}, 10000);
});
tjq('a[href="#profile"]').on('shown.bs.tab', function (e) {
	tjq(".view-profile").show();
	tjq(".edit-profile").hide();
});