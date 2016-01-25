define(["jquery"], function($) {
	
	$(document).ready(function() {
		$("[data-jslink]").each(function() {
			$(this).css("cursor", "pointer");
			var uri = $(this).attr("data-jslink");
			$(this).click(function() {
				window.location = uri;
			});
		});
	});

});