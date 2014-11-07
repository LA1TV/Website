define([
	"jquery",
	"lib/jquery.slick",
	"lib/domReady!"
], function($, FitTextHandler) {
	
	$(".page-home").first().each(function() {
		
		function animateInPage() {
			$pageContainer.animate({opacity: 1}, 500);
		}
		
		var $pageContainer = $(this).first();
		
		$pageContainer.css("display", "block");
		
		var $promoCarousel = $pageContainer.find(".promo-carousel").first();
		if ($promoCarousel.length > 0) {
			$promoCarousel.each(function() {
				var self = this;
				
				function animateFooter(show, footerPos) {
					$footers[footerPos].animate({opacity: show ? 1 : 0}, aniDuration/2);
				}
			
				$(this).css("display", "block");
				
				var $footers = [];
				
				$(this).find(".footer").each(function() {
					$footers.push($(this).first());
					if ($footers.length === 1) {
						$(this).css("opacity", 1);
					}
				});
				
				var aniDuration = 800;
				
				var slick = $(this).slick({
					dots: true,
					autoplay: true,
					autoplaySpeed: 4500,
					arrows: true,
					fade: false,
					pauseOnHover: true,
					speed: aniDuration,
					onBeforeChange: function() {
						animateFooter(false, slick.slickCurrentSlide());
					},
					onAfterChange: function() {
						animateFooter(true, slick.slickCurrentSlide());
					}
				});
				animateInPage();
			});
		}
		else {
			animateInPage();
		}
		
	});
	
});