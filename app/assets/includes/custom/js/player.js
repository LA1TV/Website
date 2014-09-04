$(document).ready(function() {
	
	$(".player-container").each(function() {
		var self = this;
		
		var playerInfoUri = $(this).attr("data-info-uri");
		var playerComponent = null;
		
		update();
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
			playerComponent.render();
		}
	});
	
});