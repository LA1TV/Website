define([
	"jquery",
	"lib/domReady!"
], function($) {
	
	$(".side-banners-container").each(function() {
		var self = this;
		
		$(".side-banner-container").each(function() {
			var bgUrl = $(this).attr("data-bg-url");
			if (bgUrl !== "") {
				$(this).css("background-image", 'url("'+bgUrl+'")');
			}
		});
		
		setTimeout(function() {
			$(self).animate({opacity: 1}, 1500, "swing");
		}, 1500);
	});

});