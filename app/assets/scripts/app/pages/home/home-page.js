define([
	"jquery",
	"lib/jquery.slick",
	"lib/domReady!"
], function($, FitTextHandler) {
	
	$(".page-home").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".promo-carousel").each(function() {
			$(this).css("display", "block");
			
			$(this).slick({
				dots: true,
				autoplay: true,
				autoplaySpeed: 4500,
				arrows: true,
				fade: false,
				pauseOnHover: true,
				speed: 800
			});
			
			
		});
		
	});
	
});