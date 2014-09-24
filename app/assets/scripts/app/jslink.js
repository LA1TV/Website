define(["jquery", "lib/domReady!"], function($) {
	
	$("[data-jslink]").each(function() {
		$(this).css("cursor", "pointer");
		var uri = $(this).attr("data-jslink");
		$(this).click(function() {
			window.location = uri;
		});
	});

});