// takes in a Player Component and handles all communication with it.
// has knowledge of the requests to the server and the responses returned

// communicates with a PlayerControllerQualitiesHander to manage quality selection.
// also manages likes, view count, and comments

var PlayerController = null;

$(document).ready(function() {
	
	// qualities handler needs to be an object with the following methods:
	// - getChosenQualityId()
	// 		should return the current chosen quality id
	// - setAvailableQualities(qualities)
	//		called with an array of {id, name}
	//		will be an empty array in the case of there being no video
	
	PlayerController = function(playerInfoUri, qualitiesHandler) {
		
		var self = this;
		
		// destroys the controller and player and prevents any future requests
		this.destroy = function() {
			// TODO
			destroyed = true;
			if (timerId !== null) {
				clearTimeout(timerId);
			}
			playerComponent.destroy();
		};
		
		this.getPlayerComponentEl = function() {
			if (playerComponent !== null) {
				return playerComponent.getEl();
			}
			return null;
		};
		
		this.getNumLikes = function() {
		
		};
		
		this.registerLike = function() {
			
		};
		
		this.getViewCount = function() {
		
		};
		
		// TODO: comments stuff
		
		var destroyed = false;
		var timerId = null;
		var playerComponent = null;
		var cachedData = null;
		
		$(qualitiesHandler).on("chosenQualityChanged", function() {
			updatePlayer();
		});
		
		// kick it off
		update();
		
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
					cachedData = data;
					updatePlayer();
				}
				
				if (!destroyed) {
					// schedule update again in 15 seconds
					timerId = setTimeout(update, 15000);
				}
			});
		}
		
		function updatePlayer() {
			if (cachedData === null) {
				return;
			}
			data = cachedData;
			if (playerComponent === null) {
				playerComponent = new PlayerComponent(data.coverUri);
				$(self).triggerHandler("playerComponentElAvailable");
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
				var qualities = [];
				var qualityIds = [];
				for (var i=0; i<data.videoUris.length; i++) {
					var videoUri = data.videoUris[i];
					qualities.push({
						id:		videoUri.quality.id,
						name:	videoUri.quality.name
					});
					qualityIds.push(videoUri.quality.id);
				}
				qualitiesHandler.setAvailableQualities(qualities);
				var chosenQualityId = qualitiesHandler.getChosenQualityId();
				var videoUri = data.videoUris[qualityIds.indexOf(chosenQualityId)];
				playerComponent.setPlayerUris(videoUri.uris);
			}
			else {
				playerComponent.showPlayer(false);
			}
			
			playerComponent.render();
		}
		
	};
});