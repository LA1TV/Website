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

			if ($(this).attr("data-found-media-item") !== "1") {
				// media item could not be found. attributes required below won't have been set
				return;
			}
			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerWatchingUri = $(this).attr("data-register-watching-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var updatePlaybackTimeBaseUri = $(this).attr("data-update-playback-time-base-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var embedded = true;
			var autoPlayVod = $(this).attr("data-auto-play-vod") === "1";
			var autoPlayStream = $(this).attr("data-auto-play-stream") === "1";
			var vodPlayStartTime = $(this).attr("data-vod-play-start-time") === "" ? null : parseInt($(this).attr("data-vod-play-start-time"));
			var ignoreExternalStreamUrl = $(this).attr("data-ignore-external-stream-url") === "1";
			var hideBottomBar = $(this).attr("data-hide-bottom-bar") === "1";
			var initialVodQualityId = $(this).attr("data-initial-vod-quality-id") === "" ? null : parseInt($(this).attr("data-initial-vod-quality-id"));
			var initialStreamQualityId = $(this).attr("data-initial-stream-quality-id") === "" ? null : parseInt($(this).attr("data-initial-stream-quality-id"));
			var disableFullScreen = $(this).attr("data-disable-full-screen") === "1";
			var placeQualitySelectionComponentInPlayer = hideBottomBar; // if the bottom bar is not visible put the quality selection inside the player
			var showTitleInPlayer = $(this).attr("data-show-title-in-player") === "1";
			var disablePlayerControls = $(this).attr("data-disable-player-controls") === "1";
			var enableSmartAutoPlay = $(this).attr("data-enable-smart-auto-play") === "1";
			
			// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
			playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerWatchingUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
			
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
				
				if (!window.postMessage) {
					// messaging api not supported in browser
					return;
				}
				
				var parent = window.parent;
				if (!parent) {
					return;
				}
				
				var playerController = playerContainer.getPlayerController();
				playerController.onLoaded(function() {
					window.onmessage = function(event) {
						var data = null;
						try {
							data = $.parseJSON(event.data);
						} catch(ex){}
						
						if (data == null) {
							return;
						}
						
						if (typeof data.playerApi.action === "string") {
							var action = data.playerApi.action;
							if (action === "STATE_UPDATE") {
								sendEvent("stateUpdate")
							}
							else if (action === "PAUSE") {
								playerController.pause();
							}
							else if (action === "PLAY") {
								playerController.play();
							}
						}
					};
					
					$(playerController).on("play", function() {
						sendEvent("play");
					});
					$(playerController).on("pause", function() {
						sendEvent("pause");
					});
					$(playerController).on("ended", function() {
						sendEvent("ended");
					});
					$(playerController).on("playerTypeChanged", function() {
						sendEvent("typeChanged");
					});
					
					sendEvent("stateUpdate");
					
					function sendEvent(eventId) {
						var data = {
							playerApi: {
								eventId: eventId,
								state: getPlayerState()
							}
						};
						parent.postMessage(JSON.stringify(data), "*");
					}
					
					function getPlayerState() {
						return {
							type: playerController.getPlayerType(),
							playing: playerController.paused() === false && !playerController.hasPlaybackErrorOccurred() // .paused() can return null if unknown
						};
					}
				});
			}
			
		});
		
		
	});
	
});