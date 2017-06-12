var $ = require("jquery");
var PageData = require("app/page-data");

$(document).ready(function() {
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
			
			function canGoFullScreen() {
				// === false to make sure fails safe if none of the properties exist
				return !(
					document.fullscreenEnabled === false ||
					document.webkitFullscreenEnabled === false ||
					document.mozFullScreenEnabled === false ||
					document.msFullscreenEnabled === false
				);
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
			var recommendationsUri = $(this).attr("data-recommendations-uri");
			var registerWatchingUri = $(this).attr("data-register-watching-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var embedded = true;
			var autoPlayVod = $(this).attr("data-auto-play-vod") === "1";
			var autoPlayStream = $(this).attr("data-auto-play-stream") === "1";
			var vodPlayStartTime = $(this).attr("data-vod-play-start-time") === "" ? null : parseInt($(this).attr("data-vod-play-start-time"));
			var muted = $(this).attr("data-muted") === "1";
			var ignoreExternalStreamUrl = $(this).attr("data-ignore-external-stream-url") === "1";
			var bottomBarMode = $(this).attr("data-hide-bottom-bar") === "1" ? "none" : "full";
			var initialVodQualityId = $(this).attr("data-initial-vod-quality-id") === "" ? null : parseInt($(this).attr("data-initial-vod-quality-id"));
			var initialStreamQualityId = $(this).attr("data-initial-stream-quality-id") === "" ? null : parseInt($(this).attr("data-initial-stream-quality-id"));
			var disableFullScreen = $(this).attr("data-disable-full-screen") === "1" || !canGoFullScreen();
			var placeQualitySelectionComponentInPlayer = bottomBarMode === "none"; // if the bottom bar is not visible put the quality selection inside the player
			var showTitleInPlayer = $(this).attr("data-show-title-in-player") === "1";
			var disablePlayerControls = $(this).attr("data-disable-player-controls") === "1";
			var enableSmartAutoPlay = $(this).attr("data-enable-smart-auto-play") === "1";
			
			// load async
			require(["app/components/player-container"], function(PlayerContainer) {
				
				// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
				playerContainer = new PlayerContainer(playerInfoUri, registerWatchingUri, registerLikeUri, recommendationsUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay, muted);
				
				playerContainer.onLoaded(function() {
					
					$(self).empty();
					$pageContainer.attr("data-loaded", "1");
					setTimeout(function() {
						$pageContainer.attr("data-animate-in", "1");
					}, 0);
					var $componentEl = playerContainer.getEl();
					$(self).append($componentEl);
					$playerContainer = $componentEl;
					updatePlayerContainerHeight();
					initializeApi();
				});
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
