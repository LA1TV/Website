$(document).ready(function() {
	
	$(".page-player").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container").each(function() {
			var self = this;
			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			
			var $bottomContainer = $("<div />").addClass("bottom-container clearfix");
			var $viewCount = $("<div />").addClass("view-count").css("display", "none");
			var $rightSection = $("<div />").addClass("right-section");
			var $likeButtonItemContainer = $("<div />").addClass("item-container");
			var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
			var $likeButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-thumbs-up");
			var $likeButtonTxt = $("<span />");
			var $overrideButton = $("<button />").attr("type", "button").addClass("override-button btn btn-default btn-xs");
			var $playerComponent = null;
			$likeButton.append($likeButtonGlyphicon);
			$likeButton.append($likeButtonTxt);
			var $qualitySelectionItemContainer = $("<div />").addClass("item-container");
			
			$bottomContainer.append($viewCount);
			$bottomContainer.append($overrideButton);
			$bottomContainer.append($rightSection);
			$rightSection.append($likeButtonItemContainer);
			$likeButtonItemContainer.append($likeButton);
			$rightSection.append($qualitySelectionItemContainer);
			
			var qualitySelectionComponent = new QualitySelectionComponent();
			$(qualitySelectionComponent).on("qualitiesChanged", function() {
				renderQualitySelectionComponent();
			});
			renderQualitySelectionComponent();
			$qualitySelectionItemContainer.append(qualitySelectionComponent.getEl());
			
			
			var playerController = new PlayerController(playerInfoUri, registerViewCountUri, registerLikeUri, qualitySelectionComponent);
			$(playerController).on("playerComponentElAvailable", function() {
				$(self).empty(); // will contain loading message initially
				$playerComponent = playerController.getPlayerComponentEl();
				$(self).append($playerComponent);
				$(self).append($bottomContainer);
				renderOverrideMode();
				renderOverrideButton();
				$overrideButton.click(function() {
					playerController.enableOverrideMode(!playerController.getOverrideModeEnabled());
				});
			});
			
			$(playerController).on("viewCountChanged playerTypeChanged", function() {
				renderViewCount();
			});
			
			$likeButton.click(function() {
				if (!loggedIn) {
					alert("Please log in to use this feature.");
					return;
				}
				$likeButton.prop("disabled", true);
				// ignoring dislikes for now. could be added in the future
				var type = playerController.getLikeType() !== "like" ? "like" : "reset";
				playerController.registerLike(type, function(success) {
					$likeButton.prop("disabled", false);
				});
			});
			
			$(playerController).on("likeTypeChanged numLikesChanged streamStateChanged playerTypeChanged", function() {
				renderLikeButton();
			});
			
			$(playerController).on("overrideModeChanged", function() {
				renderOverrideMode();
				renderOverrideButton();
			});
			
			renderViewCount();
			renderLikeButton();
			
			function renderQualitySelectionComponent() {
				if (qualitySelectionComponent.hasQualities()) {
					$qualitySelectionItemContainer.css("display", "inline-block");
				}
				else {
					$qualitySelectionItemContainer.css("display", "none");
				}
			}
			
			function renderViewCount() {
				var viewCount = playerController.getViewCount();
				if (viewCount !== null && (playerController.getPlayerType() !== "ad" || viewCount > 0)) {
					$viewCount.text(viewCount+" view"+(viewCount !== 1 ? "s":"")).css("display", "inline-block");
				}
				else {
					$viewCount.text("").css("display", "none");
				}
			}
			
			function renderLikeButton() {
				var likeType = playerController.getLikeType();
				var numLikes = playerController.getNumLikes();
				var streamState = playerController.getStreamState();
				var playerType = playerController.getPlayerType();
				var txt = null;
				// ignoring dislikes for now. maybe implement in the future
				
				// enable like button if no content unless it's an ad because stream is over.
				if (playerType === null || (playerType === "ad" && streamState !== 3)) {
					$likeButton.hide();
				}
				else {
					$likeButton.show();
				}
				
				if (likeType === "like") {
					txt = " Liked!";
				}
				else {
					txt = " Like!";
				}
				if (numLikes !== null) {
					txt = txt+" ("+playerController.getNumLikes()+")";
				}
				$likeButtonTxt.text(txt);
			}
			
			function renderOverrideMode() {
				if ($playerComponent === null) {
					return;
				}
				
				if (playerController.getOverrideModeEnabled()) {
					$playerComponent.addClass("override-mode-enabled");
				}
				else {
					$playerComponent.removeClass("override-mode-enabled");
				}
			}
			
			function renderOverrideButton() {
				if (!enableAdminOverride) {
					$overrideButton.css("display", "none");
					return;
				}
				
				if (playerController.getOverrideModeEnabled()) {
					$overrideButton.text("Disable Admin Override").removeClass("btn-default").addClass("btn-danger");
				}
				else {
					$overrideButton.text("Enable Admin Override").removeClass("btn-danger").addClass("btn-default");
				}
			}
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
					
					jQuery.ajax(baseUrl+"/admin/media/admin-stream-control-stream-state/"+mediaItemId, {
						cache: false,
						dataType: "json",
						data: {
							csrf_token: getCsrfToken(),
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
					jQuery.ajax(baseUrl+"/admin/media/admin-stream-control-info-msg/"+mediaItemId, {
						cache: false,
						dataType: "json",
						data: {
							csrf_token: getCsrfToken(),
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
	});
	
});