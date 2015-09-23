define([
	"jquery",
	"../../components/button-group",
	"../../components/comments",
	"../../components/player-container",
	"../../components/auto-continue-button",
	"../../page-data",
	"../../helpers/build-get-uri",
	"./auto-continue-manager",
	"../../device-detection",
	"../../helpers/ajax-helpers",
	"lib/jquery.cookie",
	"lib/domReady!"
], function($, ButtonGroup, CommentsComponent, PlayerContainer, AutoContinueButton, PageData, buildGetUri, AutoContinueManager, DeviceDetection, AjaxHelpers) {
	
	var playerController = null;
		
	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container-component-container").each(function() {
			var self = this;
		
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerWatchingUri = $(this).attr("data-register-watching-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var autoPlayVod = $(this).attr("data-auto-play-vod") === "1";
			var autoPlayStream = true; // always autoplay stream
			var vodPlayStartTime = $(this).attr("data-vod-play-start-time") === "" ? null : parseInt($(this).attr("data-vod-play-start-time"));
			var ignoreExternalStreamUrl = false;
			var initialVodQualityId = null;
			var initialStreamQualityId = null;
			var bottomBarMode = "full";
			var disableFullScreen = false;
			var placeQualitySelectionComponentInPlayer = false;
			var showTitleInPlayer = false;
			var embedded = false;
			var disablePlayerControls = false;
			var enableSmartAutoPlay = true;
		
			var playerContainer = new PlayerContainer(playerInfoUri, registerWatchingUri, registerLikeUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
			playerContainer.onLoaded(function() {
				$(self).empty();
				$(self).append(playerContainer.getEl());
				playerController = playerContainer.getPlayerController();
				initAutoContinue();
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
						headers: AjaxHelpers.getHeaders(),
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
						headers: AjaxHelpers.getHeaders(),
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
		
		function initAutoContinue() {
			
			// only allow the auto continue feature if not on a mobile
			// (ios devices disable autoplay on the <video> tag)
			if (DeviceDetection.isMobile()) {
				return;
			}
			
			$pageContainer.find(".playlist").each(function() {
				
				var infoUri = $(this).attr("data-info-uri");
				var initialMode = parseInt($(this).attr("data-auto-continue-mode"));
				
				var autoContinueManager = new AutoContinueManager(playerController, infoUri, initialMode);
				var autoContinueButton = new AutoContinueButton({mode: autoContinueManager.getMode()});
				
				$(autoContinueButton).on("stateChanged", function() {
					autoContinueManager.setMode(autoContinueButton.getMode());
				});
				
				$(this).find(".auto-continue-btn-item").append(autoContinueButton.getEl());
				
			});
		}
	});
	
});