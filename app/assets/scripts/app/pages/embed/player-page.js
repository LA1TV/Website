define([
	"jquery",
	"../../page-data",
	"../../components/player-container",
	"lib/domReady!"
], function($, PageData, PlayerContainer) {
	
	var notInitializedFn = function() {
		throw("The player api hasn't initialized yet. Use the \"ready\" callback.");
	};
	
	var playerApiReadyCallback = null;
	window.playerApi = {
		ready: function(callback) { // callback which is called when the rest of the api becomes available, or called immediately if it already is
			playerApiReadyCallback = callback;
		},
		getType: notInitializedFn, // either "ad", "vod", or "live"
		onTypeChanged: notInitializedFn, // callback called whenever player type changes
		playing: notInitializedFn, // true if it's either "vod" or "live" and playing
		play: notInitializedFn, // start playing. must be "vod" or "live"
		pause: notInitializedFn, // pause playback. must be "vod" or "live"
		onPlay: notInitializedFn, // callback called when play starts
		onPause: notInitializedFn, // callback called when content is paused
		onEnded: notInitializedFn, // callback called when vod or live stream reaches the end of playback
	};

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
			var updatePlaybackTimeBaseUri = $(this).attr("data-update-playback-time-base-uri");
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
			var showTitleInPlayer = $(this).attr("data-show-title-in-player") === "1";
			
			// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
			playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer);
			
			playerContainer.onLoaded(function() {
				
				$(self).empty();
				$pageContainer.attr("data-loaded", "1");
				var $componentEl = playerContainer.getEl();
				$(self).append($componentEl);
				$playerContainer = $componentEl;
				updatePlayerContainerHeight();
				initializeApi();
			});
			
			
			function updatePlayerContainerHeight() {
				var containerHeight = $pageContainer.innerHeight();
				var headingHeight = $headingContainer.outerHeight(true);
				$playerContainer.height(Math.max(containerHeight - headingHeight, 0));
				if (playerContainer !== null) {
					playerContainer.updateDimensions();
				}
				$playerContainer.show();
			}
			
			function initializeApi() {
				var playerController = playerContainer.getPlayerController();
				window.playerApi.ready = function(callback) {
					callback();
				};
				window.playerApi.getType =  function() {
					return playerController.getPlayerType();
				};
				window.playerApi.onTypeChanged = null;
				$(playerController).on("playerTypeChanged", function() {
					var callback = window.playerApi.onTypeChanged;
					if (callback) {
						callback();
					}
				});
				window.playerApi.playing = function() {
					return playerController.paused() === false; // .paused() can return null if unknown
				};
				window.playerApi.play = function() {
					playerController.play();
				};
				window.playerApi.pause = function() {
					playerController.pause();
				};
				window.playerApi.onPlay = null;
				$(playerController).on("play", function() {
					var callback = window.playerApi.onPlay;
					if (callback) {
						callback();
					}
				});
				window.playerApi.onPause = null;
				$(playerController).on("pause", function() {
					var callback = window.playerApi.onPause;
					if (callback) {
						callback();
					}
				});
				window.playerApi.onEnded = null;
				$(playerController).on("ended", function() {
					var callback = window.playerApi.onEnded;
					if (callback) {
						callback();
					}
				});
				
				if (playerApiReadyCallback) {
					// call the ready callback that has already been provided
					playerApiReadyCallback();
					playerApiReadyCallback = null;
				}
			}
			
		});
		
		
	});
	
});