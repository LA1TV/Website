define([
	"jquery",
	"lib/jquery.slick",
	"lib/domReady!"
], function($) {
	
	$(".page-home").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".promo-carousel").each(function() {
			$(this).css("display", "block");
			$(this).slick({
				dots: true
			});
		});
		
	});
	
});