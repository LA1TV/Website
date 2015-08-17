define([
	"jquery",
	"../../components/player-container",
	"lib/domReady!"
], function($, PlayerContainer) {
	
	$(".page-live-stream").first().each(function() {
		
		var $pageContainer = $(this).first();

		$pageContainer.find(".player-container-component-container").each(function() {
			var self = this;
		
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerWatchingUri = $(this).attr("data-register-watching-uri");
			var registerViewCountUri = null;
			var registerLikeUri = null;
			var updatePlaybackTimeBaseUri = null;
			var enableAdminOverride = false;
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var autoPlayVod = false; // should never be any
			var autoPlayStream = true;
			var vodPlayStartTime = null;
			var ignoreExternalStreamUrl = false;
			var initialVodQualityId = null;
			var initialStreamQualityId = null;
			var hideBottomBar = false;
			var disableFullScreen = false;
			var placeQualitySelectionComponentInPlayer = false;
			var showTitleInPlayer = false;
			var embedded = false;
			var disablePlayerControls = false;
			var enableSmartAutoPlay = true;
		
			var playerContainer = new PlayerContainer(playerInfoUri, registerViewCountUri, registerWatchingUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
			playerContainer.onLoaded(function() {
				$(self).empty();
				$(self).append(playerContainer.getEl());
			});
		});
		
	});

});