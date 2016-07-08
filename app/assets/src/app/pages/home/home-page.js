var $ = require("jquery");
var ButtonGroup = require("app/components/button-group");
require("imports?jQuery=lib/jquery!lib/jquery.flexslider");
	
$(document).ready(function() {
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
				var recommendationsUri = $(this).attr("data-recommendations-uri");
				var registerWatchingUri = $(this).attr("data-register-watching-uri");
				var registerLikeUri = $(this).attr("data-register-like-uri");
				var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
				var loginRequiredMsg = $(this).attr("data-login-required-msg");
				var autoPlayVod = false;
				var autoPlayStream = false;
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
				
				// load async
				require(["app/components/player-container"], function(PlayerContainer) {
					var playerContainer = new PlayerContainer(playerInfoUri, registerWatchingUri, registerLikeUri, recommendationsUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
					playerContainer.onLoaded(function() {
						$(self).append(playerContainer.getEl());
						animatePageIn();
					});
				});
			});
		}
		else {
			animatePageIn();
		}

		var $listSelectionButtonGroup = $pageContainer.find(".list-selection-button-group").first();
		if ($listSelectionButtonGroup.length > 0) {

			var $lists = $pageContainer.find(".lists");
			var $mostPopularListHolder = $lists.find(".most-popular-section .list-holder");
			var $recentlyAddedListHolder = $lists.find(".recently-added-section .list-holder");

			var buttonGroup = new ButtonGroup([
				{
					id: "mostPopular",
					text: "Most Popular"
				},
				{
					id: "recentlyAdded",
					text: "Recently Added"
				}
			], true, {id: "mostPopular"});

			$listSelectionButtonGroup.append(buttonGroup.getEl());

			$(buttonGroup).on("stateChanged", updateChosenList);

			var hideTimerId = null;
			var currentList = buttonGroup.getId();

			function updateChosenList() {
				var list = buttonGroup.getId();
				if (list === currentList) {
					return;
				}
				currentList = list;
				$mostPopularListHolder.removeClass("hidden");
				$recentlyAddedListHolder.removeClass("hidden");
				$lists.attr("data-list", list);
				if (hideTimerId !== null) {
					clearTimeout(hideTimerId);
				}
				hideTimerId = setTimeout(function() {
					hideTimerId = null;
					if (list === "mostPopular") {
						$recentlyAddedListHolder.addClass("hidden");
					}
					else {
						$mostPopularListHolder.addClass("hidden");
					}
				}, 1100);
			}
		}
	});
});