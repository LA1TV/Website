define([
	"jquery",
	"../../components/player-container",
	"lib/domReady!"
], function($, PlayerContainer) {

	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();
		var playerContainer = null;
		
		$pageContainer.find(".player-container").each(function() {
			var self = this;
			
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerViewCountUri = $(this).attr("data-register-view-count-uri");
			var registerLikeUri = $(this).attr("data-register-like-uri");
			var enableAdminOverride = $(this).attr("data-enable-admin-override") === "1";
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var responsive = !$(this).hasClass("embedded-player-container");
		
			// replace the player-container on the dom with the PlayerContainerComponent element when the component has loaded.
			playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerLikeUri, enableAdminOverride, loginRequiredMsg, responsive);
			playerContainer.onLoaded(function() {
				// replace the player container dom el with the component el.
				// the dom el may currently contain a hard coded loading message.
				var $componentEl = playerContainer.getEl();
				if ($(self).hasClass("embedded-player-container")) {
					// TODO: must be a better way
					$componentEl.addClass("embedded-player-container");
				}
				$(self).replaceWith($componentEl);
				$playerContainer = $componentEl;
				updatePlayerContainerHeight();
			});
		});
		
		var $headingContainer = $pageContainer.find(".heading-container");
		var $playerContainer = $pageContainer.find(".player-container");
		
		$(window).resize(updatePlayerContainerHeight);
		updatePlayerContainerHeight();
		
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