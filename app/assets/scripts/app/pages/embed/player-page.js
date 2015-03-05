define([
	"jquery",
	"../../page-data",
	"../../components/player-container",
	"lib/domReady!"
], function($, PageData, PlayerContainer) {

	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();	
		var $headingContainer = $pageContainer.find(".heading-container");
	
		
		$pageContainer.find(".player-container-component-container").each(function() {
			
			function inIframe() {
				try {
					return window.self !== window.top;
				} catch (e) {
					// fail safe
					return true;
				}
			}
			
			var self = this;
			
			var siteUri = $(this).attr("data-site-uri");
			var disableRedirect = $(this).attr("data-disable-redirect") === "1";
			if (PageData.get("env") !== "local" && !disableRedirect && !inIframe()) {
				// this is not in an iframe. it should be
				alert("This content is not embedded correctly.\n\nYou will now be redirected to our website instead.");
				// redirect the user to the corresponding page on the main site
				window.location = siteUri;
				return;
			}
			
			var playerContainer = null;
			var $playerContainer = $(this).find(".msg-container");
			
			$(window).resize(updatePlayerContainerHeight);
			updatePlayerContainerHeight();

			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var updatePlaybackTimeUri = $(this).attr("data-update-playback-time-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var embedded = true;
			var autoPlayVod = $(this).attr("data-auto-play-vod") === "1";
			var autoPlayStream = $(this).attr("data-auto-play-stream") === "1";
			var vodPlayStartTime = null;
			var ignoreExternalStreamUrl = $(this).attr("data-ignore-external-stream-url") === "1";
			var hideBottomBar = $(this).attr("data-hide-bottom-bar") === "1";
			var initialVodQualityId = $(this).attr("data-initial-vod-quality-id") === "" ? null : parseInt($(this).attr("data-initial-vod-quality-id"));
			var initialStreamQualityId = $(this).attr("data-initial-stream-quality-id") === "" ? null : parseInt($(this).attr("data-initial-stream-quality-id"));
			var disableFullScreen = $(this).attr("data-disable-full-screen") === "1";
			var placeQualitySelectionComponentInPlayer = hideBottomBar; // if the bottom bar is not visible put the quality selection inside the player
			
			// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
			playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer);
			
			
			playerContainer.onLoaded(function() {
				
				$(self).empty();
				var $componentEl = playerContainer.getEl();
				$(self).append($componentEl);
				$playerContainer = $componentEl;
				updatePlayerContainerHeight();
			});
			
			
			function updatePlayerContainerHeight() {
				console.log("resizing");
				var containerHeight = $pageContainer.innerHeight();
				var headingHeight = $headingContainer.outerHeight(true);
				$playerContainer.height(Math.max(containerHeight - headingHeight, 0));
				if (playerContainer !== null) {
					playerContainer.updateDimensions();
				}
				$playerContainer.show();
			}
			
		});
		
		
	});
	
});