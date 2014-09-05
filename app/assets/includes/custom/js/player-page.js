$(document).ready(function() {
	
	$(".page-player").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container").each(function() {
			var self = this;
			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			
			var $bottomContainer = $("<div />").addClass("bottom-container clearfix");
			var $viewCount = $("<div />").addClass("view-count").css("display", "none");
			var $rightSection = $("<div />").addClass("right-section");
			var $likeButtonItemContainer = $("<div />").addClass("item-container");
			var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs").html('<span class="glyphicon glyphicon-thumbs-up"></span> Like!');
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
			
			
			var playerController = new PlayerController(playerInfoUri, registerViewCountUri, qualitySelectionComponent);
			$(playerController).on("playerComponentElAvailable", function() {
				$(self).empty(); // will contain loading message initially
				$(self).append(playerController.getPlayerComponentEl());
				$(self).append($bottomContainer);
			});
			
			$(playerController).on("viewCountChanged playerTypeChanged", function() {
				renderViewCount();
			});
			
			renderViewCount();
			
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
		});
		

	});
	
});