define([
	"jquery",
	"../../components/player-container",
	"lib/domReady!"
], function($, PlayerContainer) {

	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		var $headingContainer = $pageContainer.find(".heading-container");
		
		$pageContainer.find(".player-container-component-container").each(function() {
			var self = this;
			
			var playerContainer = null;
			var $playerContainer = $(this).find(".msg-container");
			
			$(window).resize(updatePlayerContainerHeight);
			updatePlayerContainerHeight();

			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var embedded = true;
		
			// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
			playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, enableAdminOverride, loginRequiredMsg, embedded);
			
			
			playerContainer.onLoaded(function() {
				
				$(self).empty();
				var $componentEl = playerContainer.getEl();
				$(self).append($componentEl);
				$playerContainer = $componentEl;
				updatePlayerContainerHeight();
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
			
		});
		
		
	});
	
});