define([
	"jquery",
	"../../components/player-container",
	"lib/jquery.flexslider",
	"lib/domReady!"
], function($, PlayerContainer) {
	
	$(".page-home").first().each(function() {
		
		var $pageContainer = $(this).first();
		var $wrapper = $pageContainer.find(".wrapper").first();
		$wrapper.removeClass("hidden");

		var loadCount = 2;
		function animatePageIn() {
			if (--loadCount === 0) {
				setTimeout(function() {
					$wrapper.attr("data-animate-in", "1");
				}, 0);
			}
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

		var $promoItem = $pageContainer.find(".promo-item-container").first();
		if ($promoItem.length > 0) {
			$promoItem.find(".player-container-component-container").each(function() {
				var self = this;
			
				var playerInfoUri = $(this).attr("data-info-uri");
				var registerWatchingUri = $(this).attr("data-register-watching-uri");
				var registerLikeUri = $(this).attr("data-register-like-uri");
				var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
				var loginRequiredMsg = $(this).attr("data-login-required-msg");
				var autoPlayVod = true;
				var autoPlayStream = true;
				var vodPlayStartTime = null;
				var ignoreExternalStreamUrl = true;
				var initialVodQualityId = null;
				var initialStreamQualityId = null;
				var bottomBarMode = "compact";
				var disableFullScreen = false;
				var placeQualitySelectionComponentInPlayer = false;
				var showTitleInPlayer = true;
				var embedded = false;
				var disablePlayerControls = false;
				var enableSmartAutoPlay = true;
			
				var playerContainer = new PlayerContainer(playerInfoUri, registerWatchingUri, registerLikeUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
				playerContainer.onLoaded(function() {
					$(self).append(playerContainer.getEl());
					animatePageIn();
				});
			});
		}
		else {
			animatePageIn();
		}
		
	});
	
});