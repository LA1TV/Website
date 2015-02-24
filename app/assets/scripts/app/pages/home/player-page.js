define([
	"jquery",
	"../../components/button-group",
	"../../components/comments",
	"../../components/player-container",
	"../../page-data",
	"../../helpers/build-get-uri",
	"lib/domReady!"
], function($, ButtonGroup, CommentsComponent, PlayerContainer, PageData, buildGetUri) {
	
	var playerController = null;
	
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
			var autoPlay = $(this).attr("data-autoplay") === "1";
			var vodPlayStartTime = $(this).attr("data-vod-play-start-time") === "" ? null : parseInt($(this).attr("data-vod-play-start-time"));
			var ignoreExternalStreamUrl = false;
			var hideBottomBar = false;
			var embedded = false
		
			var playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlay, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar);
			playerContainer.onLoaded(function() {
				$(self).empty();
				$(self).append(playerContainer.getEl());
				playerController = playerContainer.getPlayerController();
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
		
	});
	
});