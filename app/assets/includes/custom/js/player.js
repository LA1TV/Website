$(document).ready(function() {
	
	$(".player-container").each(function() {
		var self = this;
		
		var playerInfoUri = $(this).attr("data-info-uri");
		var playerComponent = null;
		
		update();
		// TODO: wait for previous request response before trying again
		setInterval(update, 15000);
		
		function update() {
			jQuery.ajax(playerInfoUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: getCsrfToken()
				},
				type: "POST"
			}).done(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					updatePlayer(data);
				}
			});
		}
		
		function updatePlayer(data) {
			if (playerComponent === null) {
				playerComponent = new PlayerComponent(data.coverUri);
				$(self).append(playerComponent.getEl());
			}
			playerComponent.setStartTime(data.scheduledPublishTime !== null ? new Date(data.scheduledPublishTime) : null);
			playerComponent.setCustomMsg(data.streamInfoMsg);
			playerComponent.showStreamOver(data.streamState === 3);
			playerComponent.showVodAvailableShortly(data.streamState === 3 && data.availableOnDemand);
			if (data.streamState === 2) {
				// stream should be live
				playerComponent.setPlayerType("live").showPlayer(true);
			}
			else if (data.vodLive) {
				// video should be live
				playerComponent.setPlayerType("vod").showPlayer(true);
				// TODO: this needs changing to support qualities
				var videoUri = data.videoUris[0];
				playerComponent.setPlayerUris(videoUri.uris);
			}
			else {
				playerComponent.showPlayer(false);
			}
			
			playerComponent.render();
		}
	});
	
});