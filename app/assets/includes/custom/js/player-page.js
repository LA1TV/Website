$(document).ready(function() {
	
	$(".page-player").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container").each(function() {
			var self = this;
			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			
			var $bottomContainer = $("<div />").addClass("bottom-container clearfix");
			var $viewCount = $("<div />").addClass("view-count").css("display", "none");
			var $rightSection = $("<div />").addClass("right-section");
			var $likeButtonItemContainer = $("<div />").addClass("item-container");
			var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
			var $likeButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-thumbs-up");
			var $likeButtonTxt = $("<span />");
			$likeButton.append($likeButtonGlyphicon);
			$likeButton.append($likeButtonTxt);
			var $qualitySelectionItemContainer = $("<div />").addClass("item-container");
			
			$bottomContainer.append($viewCount);
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
				$(self).append(playerController.getPlayerComponentEl());
				$(self).append($bottomContainer);
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
			
			$(playerController).on("likeTypeChanged numLikesChanged", function() {
				renderLikeButton();
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
				// ignoring dislikes for now. maybe implement in the future
				if (likeType === "like") {
					$likeButtonTxt.text(" Liked!");
				}
				else {
					$likeButtonTxt.text(" Like!");
				}
			}
		});
		

	});
	
});