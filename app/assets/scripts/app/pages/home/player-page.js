define([
	"jquery",
	"../../components/button-group",
	"../../components/comments",
	"../../components/player-container",
	"../../page-data",
	"../../helpers/build-get-uri",
	"../../cookie-config",
	"lib/jquery.cookie",
	"lib/domReady!"
], function($, ButtonGroup, CommentsComponent, PlayerContainer, PageData, buildGetUri, CookieConfig) {
	
	var autoPlayState = getAutoPlayStateFromCookie(); // 0=off, 1=auto continue, 2=auto continue and loop
	
	var playerController = null;
	var autoPlayVod = null;
	var autoPlayStream = null;
		
	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container-component-container").each(function() {
			var self = this;
		
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var updatePlaybackTimeBaseUri = $(this).attr("data-update-playback-time-base-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			autoPlayVod = $(this).attr("data-auto-play-vod") === "1";
			autoPlayStream = true; // always autoplay stream
			var vodPlayStartTime = $(this).attr("data-vod-play-start-time") === "" ? null : parseInt($(this).attr("data-vod-play-start-time"));
			var ignoreExternalStreamUrl = false;
			var initialVodQualityId = null;
			var initialStreamQualityId = null;
			var hideBottomBar = false;
			var disableFullScreen = false;
			var placeQualitySelectionComponentInPlayer = false;
			var showTitleInPlayer = false;
			var embedded = false
			
			var resolvedAutoPlayVod = autoPlayVod;
			var resolvedAutoPlayStream = autoPlayStream;
			
			if (autoPlayState !== 0) {
				// auto continue is enabled so this should auto play from the beginning
				resolvedAutoPlayVod = resolvedAutoPlayStream = true;
				vodPlayStartTime = 0;
			}
		
			var playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, resolvedAutoPlayVod, resolvedAutoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer);
			playerContainer.onLoaded(function() {
				$(self).empty();
				$(self).append(playerContainer.getEl());
				playerController = playerContainer.getPlayerController();
				initAutoPlayControl();
			});
		});
		
		$pageContainer.find(".admin-panel").each(function() {
			
			var mediaItemId = parseInt($(this).attr("data-mediaitemid"));
			
			$(this).find(".stream-state-row .state-buttons").each(function() {
				var self = this;
				
				var buttonsData = jQuery.parseJSON($(this).attr("data-buttonsdata"));
				var chosenId = parseInt($(this).attr("data-chosenid"));
				var currentId = chosenId;
				var buttonGroup = new ButtonGroup(buttonsData, true, {
					id: chosenId
				});
				$(buttonGroup).on("stateChanged", function() {
					makeAjaxRequest(buttonGroup.getId());
				});
				
				$(this).append(buttonGroup.getEl());
				
				function makeAjaxRequest(id) {
					if (id === currentId) {
						// only make request if id has changed.
						return;
					}
					
					jQuery.ajax(PageData.get("baseUrl")+"/admin/media/admin-stream-control-stream-state/"+mediaItemId, {
						cache: false,
						dataType: "json",
						data: {
							csrf_token: PageData.get("csrfToken"),
							action: "stream-state",
							stream_state: id
						},
						type: "POST"
					}).always(function(data, textStatus, jqXHR) {
						if (jqXHR.status === 200) {
							currentId = data.streamState;
						}
						else {
							buttonGroup.setState({id: currentId});
						}
					});
				}
			});
			
			$(this).find(".information-msg-section").each(function() {
				var self = this;
				
				var $textarea = $(this).find("textarea");
				var currentVal = $textarea.val();
				var $updateButton = $(this).find(".update-button");
				var $revertButton = $(this).find(".revert-button");
				
				$updateButton.click(function() {
					update();
				});
				
				$revertButton.click(function() {
					disableButtons(true);
					if (!confirm("Are you sure you want to undo changes?")) {
						disableButtons(false);
						return;
					}
					$textarea.val(currentVal);
					disableButtons(false);
				});
				
				function disableButtons(disable) {
					$updateButton.prop("disabled", disable);
					$revertButton.prop("disabled", disable);
					$textarea.prop("disabled", disable);
				}
				
				function update() {
					
					var msg = $textarea.val();
					disableButtons(true);
					if (msg.length > 500) {
						alert("The message must be less than 500 characters.");
						disableButtons(false);
						return;
					}
					
					disableButtons(true);
					jQuery.ajax(PageData.get("baseUrl")+"/admin/media/admin-stream-control-info-msg/"+mediaItemId, {
						cache: false,
						dataType: "json",
						data: {
							csrf_token: PageData.get("csrfToken"),
							info_msg: msg
						},
						type: "POST"
					}).always(function(data, textStatus, jqXHR) {
						if (jqXHR.status === 200) {
							currentVal = data.infoMsg;
							alert("Updated successfully!");
						}
						else {
							alert("Failed to update message. Please try again.");
						}
						disableButtons(false);
					});
				}
			});
		});
		
		
		$pageContainer.find(".comments-container").each(function() {
			var mediaItemId = parseInt($(this).attr("data-mediaitemid"));
			var getUri = $(this).attr("data-get-uri");
			var postUri = $(this).attr("data-post-uri");
			var deleteUri = $(this).attr("data-delete-uri");
			var canPostAsFacebookUser = $(this).attr("data-can-comment-as-facebook-user") === "1";
			var canPostAsStation = $(this).attr("data-can-comment-as-station") === "1";
			var commentsComponent = new CommentsComponent(getUri, postUri, deleteUri, canPostAsFacebookUser, canPostAsStation);
			$(this).empty(); // remove loading message.
			$(this).append(commentsComponent.getEl());
		});
		
		$pageContainer.find(".chapter-selection-table").each(function() {
			
			function supportsHistoryApi() {
				return !!(window.history && history.pushState);
			}
			
			$(this).find("tr").each(function() {
				var time = parseInt($(this).attr("data-time"));
				$(this).click(function() {
					if (playerController !== null) {
						// jump to the time in the player and start playing if not already.
						playerController.jumpToTime(time, true);
						if (supportsHistoryApi()) {
							var uri = window.location.href;
							var uriWithoutParams = null;
							var startPos = uri.indexOf("?");
							if (startPos !== -1) {
								uriWithoutParams = uri.substr(0, startPos);
							}
							else {
								uriWithoutParams = uri;
							}
							
							var minutes = Math.floor(time/60);
							var seconds = time % 60;
							
							var newUri = uriWithoutParams + buildGetUri({
								t: minutes+"m"+seconds+"s"
							});
							window.history.replaceState({}, "", newUri);
						}
					}
				});
			});
		});
		
		
		// handle autoplay
		function initAutoPlayControl() {
			$pageContainer.find(".playlist").each(function() {
				
				var currentMediaItemId = parseInt($(this).attr("data-current-media-item-id"));
				var infoUri = $(this).attr("data-info-uri");
				
				// get reference to the autoplay button
				var $autoPlayBtnItem = $(this).find(".auto-play-btn-item").first();
				var $autoPlayBtn = $(this).find(".auto-play-btn").first();
				
				$autoPlayBtnItem.css("display", "inline-block");
				
				$autoPlayBtn.click(function() {
					if (shifted && autoPlayState !== 2) {
						// shift key being held down
						autoPlayState = 2;
					}
					else if (autoPlayState === 0) {
						autoPlayState = 1;
					}
					else {
						autoPlayState = 0;
					}
					
					if (autoPlayState !== 0) {
						playerController.setAutoPlayVod(true);
						playerController.setAutoPlayStream(true);
					}
					else {
						playerController.setAutoPlayVod(autoPlayVod);
						playerController.setAutoPlayStream(autoPlayStream);
					}
					render();
				});
				
				render();
				
				$(playerController).on("vodEnded streamStopped", function() {
					setTimeout(checkAndMoveOn, 0);
				});
				
				function render() {
					$autoPlayBtn.removeClass("btn-default btn-info btn-danger active");
					if (autoPlayState === 0) {
						$autoPlayBtn.addClass("btn-default");
					}
					else if (autoPlayState === 1) {
						$autoPlayBtn.addClass("active btn-info");
					}
					else if (autoPlayState === 2) {
						$autoPlayBtn.addClass("active btn-danger");
					}
					else {
						throw "Unknown auto play state.";
					}
					writeAutoPlayStateToCookie(autoPlayState);
					$autoPlayBtn.attr("aria-pressed", autoPlayState !== 0);
				}
				
				// determine if should move onto something else, and do it if necessary
				var moveOnCheckInProgress = false;
				function checkAndMoveOn() {
					if (autoPlayState === 0) {
						// autoplay disabled
						return;
					}
					
					if (moveOnCheckInProgress) {
						// check is already ongoing so no point starting another.
						return;
					}
					
					if (!allowedToMoveOn()) {
						// try again in 8 seconds
						setTimeout(checkAndMoveOn, 8000);
						return;
					}
					moveOnCheckInProgress = true;
					// determine where to go next
					jQuery.ajax(infoUri, {
						cache: false,
						dataType: "json",
						data: {
							csrf_token: PageData.get("csrfToken")
						},
						type: "POST"
					}).done(function(data) {
						if (!allowedToMoveOn()) {
							moveOnCheckInProgress = false;
							return;
						}
						var foundCurrentItem = false;
						var mediaItemToRedirectTo = null;
						for (var j=0; j===0 || (j<2 && mediaItemToRedirectTo === null && autoPlayState === 2); j++) {
							for (var i=0; i<data.length; i++) {
								var mediaItem = data[i];
								if (foundCurrentItem) {
									if ((mediaItem.vod !== null && mediaItem.vod.available) || (mediaItem.stream !== null && mediaItem.stream.state === 2)) {
										// has accessible vod, or stream which is live
										mediaItemToRedirectTo = mediaItem;
										break;
									}
								}
								if (mediaItem.id === currentMediaItemId) {
									foundCurrentItem = true;
								}
							}
							
							if (!foundCurrentItem) {
								// the current item has disappeared for some reason
								// pretend found it and run through the loop again to get first media item that is ready
								foundCurrentItem = true;
							}
						}
						if (mediaItemToRedirectTo === null) {
							moveOnCheckInProgress = false;
							// try again in 8 seconds
							setTimeout(checkAndMoveOn, 8000);
							return;
						}
						// redirect to next media item
						window.location = mediaItemToRedirectTo.url;
					}).fail(function() {
						moveOnCheckInProgress = false;
						// try again in 8 seconds
						setTimeout(checkAndMoveOn, 8000);
					});
				}
				
				// determine if we are allowed to move on
				var pageLoadTime = new Date().getTime();
				function allowedToMoveOn() {
					if (new Date().getTime() - pageLoadTime < 15000) {
						// less than 15 seconds have passed since the page loaded.
						// don't allow moving on yet to make sure don't start a dos attack!
						return false;
					}
					return autoPlayState !== 0 && !(playerController.getPlayerType() === "live" || (playerController.getPlayerType() === "vod" && !playerController.hasVodEnded()));
				}
				
				var shifted = false;
				$(document).on('keyup keydown', function(e){
					shifted = e.shiftKey;
					return true;
				});
			});
		}
	});
	
	function getAutoPlayStateFromCookie() {
		var state = $.cookie("autoPlayState");
		if (!state) {
			return 0;
		}
		return parseInt(state);
	}
	
	function writeAutoPlayStateToCookie(state) {
		var config = $.extend({}, CookieConfig, {expires: 1});
		$.cookie("autoPlayState", state, config)
	}
	
});