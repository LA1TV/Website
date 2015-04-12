define([
	"jquery",
	"lib/domReady!"
], function($) {

	$(".side-banners-container").each(function() {
		var self = this;
		setTimeout(function() {
			$(self).animate({opacity: 1}, 1500, "swing");
		}, 1500);
	});

});