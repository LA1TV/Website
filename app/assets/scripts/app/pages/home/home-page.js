define([
	"jquery",
	"lib/jquery.flexslider",
	"lib/domReady!"
], function($) {
	
	$(".page-home").first().each(function() {
		
		var $pageContainer = $(this).first();
		var $wrapper = $pageContainer.find(".wrapper").first();
		$wrapper.removeClass("hidden");

		function animatePageIn() {
			setTimeout(function() {
				$wrapper.attr("data-animate-in", "1");
			}, 0);
		}

		var $promoCarousel = $pageContainer.find(".promo-carousel").first();
		if ($promoCarousel.length > 0) {
			$promoCarousel.each(function() {
				var self = this;
				var $carousel = $(this).first();
				
				var aniDuration = 800;

				$carousel.flexslider({
					animation: "slide",
					touch: true,
					slideshow: true,
					slideshowSpeed: 4000,
					fadeFirstSlide: false,
					animationSpeed: aniDuration,
					animationLoop: false,
					pauseOnAction: true,
					pauseOnHover: true,
					controlNav: true,
					directionNav: true,
					allowOneSlide: false,
					pauseText: "",
					playText: "",
					prevText: "",
					nextText: "",
					before: function(slider) {
						$carousel.attr("data-animate", "0");
						setTimeout(function() {
							$carousel.attr("data-animate", "1");
						});
					},
					start: function() {
						$carousel.attr("data-animate", "1");
						animatePageIn();
					}
				});
			});
		}
		else {
			animatePageIn();
		}
		
	});
	
});