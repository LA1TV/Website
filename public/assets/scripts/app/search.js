define(["jquery", "./helpers/build-get-uri", "lib/domReady!"], function($, buildGetUri) {
	
	$(".search-box").each(function() {
		$(this).find(".search-input").keypress(function(e) {
		
			if (e.which !== 13) {
				return;
			}
			
			var searchTxt = $(this).val();
			
			var uri = buildGetUri({pg:1, search:searchTxt});
			window.location = window.location.href.split('?')[0] + uri;
		});
	});
	
});