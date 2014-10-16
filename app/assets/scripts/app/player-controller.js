// takes in a Player Component and handles all communication with it.
// has knowledge of the requests to the server and the responses returned

// communicates with a PlayerControllerQualitiesHander to manage quality selection.
// also manages likes and view count

define([
	"jquery",
	"./components/player",
	"./page-data",
	"lib/domReady!"
], function($, PlayerComponent, PageData) {
	var PlayerController = null;

	// qualities handler needs to be an object with the following methods:
	// - getChosenQualityId()
	// 		should return the current chosen quality id
	// - setAvailableQualities(qualities)
	//		called with an array of {id, name}
	//		will be an empty array in the case of there being no video
	
	PlayerController = function(playerInfoUri, registerViewCountUri, registerLikeUri, qualitiesHandler, responsive, autoPlay) {
		
		var self = this;
		
		// destroys the controller and player and prevents any future requests
		this.destroy = function() {
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
			return numLikes;
		};
		
		this.getNumDislikes = function() {
			return numDisikes;
		};
		
		// returns 'like' if person liked this, 'dislike' if disliked, or null if neither
		this.getLikeType = function() {
			return likeType;
		};
		
		this.registerLike = function(type, callback) {
			registerLike(type, callback);
		};
		
		this.getStreamViewCount = function() {
			return streamViewCount;
		};
		
		this.getVodViewCount = function() {
			return vodViewCount;
		};
		
		this.getViewCount = function() {
			if (cachedData !== null) {
				var count = 0;
				if (self.getStreamViewCount() !== null) {
					count += self.getStreamViewCount();
				}
				if (self.getVodViewCount() !== null) {
					count += self.getVodViewCount();
				}
				return count;
			}
			return null;
		};
		
		// pause whatever is playing if there is something
		this.pause = function() {
			if (playerComponent !== null) {
				playerComponent.pause();
			}
		};
		
		this.getPlayerType = function() {
			return playerType;
		}
		
		// 1=not live, 2=live, 3=show over, null=no live stream
		this.getStreamState = function() {
			return streamState;
		};
		
		this.getEmbedData = function() {
			return embedData;
		};
		
		this.enableOverrideMode = function(enable) {
			queuedOverrideModeEnabled = enable;
			render();
		};
		
		this.getOverrideModeEnabled = function() {
			return overrideModeEnabled;
		};

		var destroyed = false;
		var timerId = null;
		var playerComponent = null;
		var playerType = null;
		var currentUris = [];
		var cachedData = null;
		var vodViewCount = null;
		var streamViewCount = null;
		var numLikes = null;
		var numDislikes = null;
		var likeType = null; // "like", "dislike" or null
		var streamState = null;
		var overrideModeEnabled = null;
		var queuedOverrideModeEnabled = false;
		var embedData = null;
		var viewCountRegistered = false;
		
		
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
					csrf_token: PageData.get("csrfToken")
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					cachedData = data;
					render();
				}
				
				if (!destroyed) {
					// schedule update again in 15 seconds
					timerId = setTimeout(update, 15000);
				}
			});
		}
		
		function render() {
			updateEmbedData();
			updateOverrideMode();
			updatePlayer();
			updateViewCounts();
			updateLikes();
		}
		
		function updateEmbedData() {
			if (embedData !== null) {
				// the assumption is that embed data doesn't change
				return;
			}
			embedData = cachedData.embedData;
			$(self).triggerHandler("embedDataAvailable");
		}
		
		function updateOverrideMode() {
			if (queuedOverrideModeEnabled !== overrideModeEnabled) {
				overrideModeEnabled = queuedOverrideModeEnabled;
				$(self).triggerHandler("overrideModeChanged");
			}
		}
		
		function updatePlayer() {
			if (cachedData === null) {
				return;
			}
			data = cachedData;
			
			if (playerComponent === null) {
				playerComponent = new PlayerComponent(data.coverUri, responsive);
				$(self).triggerHandler("playerComponentElAvailable");
				$(playerComponent).on("play", function() {
					if (!viewCountRegistered) {
						viewCountRegistered = true;
						registerViewCount();
					}
				});
			}
			
			var queuedPlayerType = "ad";
			// live streams take precedence over vod
			if ((data.hasStream && data.streamState === 2) || (overrideModeEnabled && data.streamUris.length > 0 && data.streamState !== 3)) {
				queuedPlayerType = "live";
			}
			else if ((data.hasVod && data.vodLive && (!data.hasStream || data.streamState !== 1)) || (overrideModeEnabled && data.videoUris.length > 0)) {
				queuedPlayerType = "vod";
			}
			
			var uriGroups = [];
			if (queuedPlayerType === "live") {
				uriGroups = data.streamUris;
			}
			else if (queuedPlayerType === "vod") {
				uriGroups = data.videoUris;
			}
			var chosenUris = getChosenUris(uriGroups);
			
			var urisChanged = false;
			// only check if the uris have changes if it's still the same player type
			if (queuedPlayerType === playerType) {
				if (currentUris.length !== chosenUris.length) {
					urisChanged = true;
				}
				else {
					for(var i=0; i<chosenUris.length; i++) {
						var current = currentUris[i];
						var pending = chosenUris[i];
						if (current.uri !== pending.uri || current.type !== pending.type || current.supportedDevices !== pending.supportedDevices) {
							urisChanged = true;
							break;
						}
					}
				}
			}
			currentUris = chosenUris;
			
			if (queuedPlayerType !== playerType || urisChanged) {
				// either the player type has changed, or the current uris for the player have changed.
				// this may be down to the user changing quality or changed remotely for some reason
				setPlayerType(queuedPlayerType);
				if (queuedPlayerType === "live") {
					// auto start live stream
					playerComponent.setPlayerStartTime(0, true);
				}
				else if (queuedPlayerType === "vod") {
					if (urisChanged) {
						// reason we're here is because uris have changed. could be quality change or other reason
						// but it makes sense to automatically resume playback from where the user was previously
						playerComponent.setPlayerStartTime(playerComponent.getPlayerCurrentTime(), !playerComponent.paused());
					}
				}
				playerComponent.setPlayerUris(chosenUris);
			}
			
			if (queuedPlayerType === "ad") {
				if (data.hasStream && data.streamState === 1) {
					// show stream info message if the stream is enabled and is "not live"
					playerComponent.setCustomMsg(data.streamInfoMsg);
				}
				qualitiesHandler.setAvailableQualities([]);
			}
			playerComponent.showStreamOver(data.hasStream && data.streamState === 3);
			playerComponent.setCustomMsg(data.hasStream && data.streamState === 1 ? data.streamInfoMsg : "");
			playerComponent.showVodAvailableShortly(data.hasStream && data.streamState === 3 && data.availableOnDemand);
			playerComponent.setStartTime(data.scheduledPublishTime !== null && (!data.hasStream || data.streamState !== 3) ? new Date(data.scheduledPublishTime*1000) : null, data.hasStream);
			if (data.streamState !== streamState) {
				streamState = data.streamState;
				$(self).triggerHandler("streamStateChanged");
			}
			if (playerType !== queuedPlayerType) {
				playerType = queuedPlayerType;
				$(self).triggerHandler("playerTypeChanged");
			}
			playerComponent.render();
		}
		
		// updates the quality selection component using uriGroups and then queries it to decide what uris should be used
		function getChosenUris(uriGroups) {
			var uris = [];
			var qualities = [];
			var qualityIds = [];
			for (var i=0; i<uriGroups.length; i++) {
				var uriGroup = uriGroups[i];
				qualities.push({
					id:		uriGroup.quality.id,
					name:	uriGroup.quality.name
				});
				qualityIds.push(uriGroup.quality.id);
			}
			qualitiesHandler.setAvailableQualities(qualities);
			if (qualities.length > 0) {
				var currentQualityId = qualitiesHandler.getChosenQualityId();
				var chosenUriGroup = uriGroups[qualityIds.indexOf(currentQualityId)];
				uris = chosenUriGroup.uris;
			}
			return uris;
		}
		
		// updates the quality selection component so it has the correct qualities, then asks it what quality to use, then sends the uri group corresponding to that quality to the player
		function setPlayerComponentPlayerUris(uriGroups) {
			playerComponent.setPlayerUris(getChosenUris(uriGroups));
		}
		
		function setPlayerType(type) {
			if (type !== "live" && type !== "vod" && type !== "ad") {
				throw "Type must be either 'live', 'vod' or 'ad'.";
			}
			
			if (type === "ad") {
				playerComponent.showPlayer(false);
			}
			else {
				playerComponent.setPlayerType(type).showPlayer(true);
			}
		}
		
		function updateViewCounts() {
			var changed = false;
			var vodCountChanged = false;
			var streamCountChanged = false;
			vodCountChanged = vodViewCount !== cachedData.vodViewCount;
			vodViewCount = cachedData.vodViewCount;
			streamCountChanged = streamViewCount !== cachedData.streamViewCount;
			streamViewCount = cachedData.streamViewCount;
			if (vodCountChanged) {
				$(self).triggerHandler("vodViewCountChanged");
			}
			if (streamCountChanged) {
				$(self).triggerHandler("streamViewCountChanged");
			}
			if (vodCountChanged || streamCountChanged) {
				$(self).triggerHandler("viewCountChanged");
			}
		}
		
		function updateLikes() {
			var changed = numLikes !== cachedData.numLikes;
			numLikes = cachedData.numLikes;
			if (changed) {
				$(self).triggerHandler("numLikesChanged");
			}
			var changed = numDislikes !== cachedData.numDislikes;
			numDislikes = cachedData.numDislikes;
			if (changed) {
				$(self).triggerHandler("numDisikesChanged");
			}
			var changed = likeType !== cachedData.likeType;
			likeType = cachedData.likeType;
			if (changed) {
				$(self).triggerHandler("likeTypeChanged");
			}
		}
		
		function registerViewCount() {
			jQuery.ajax(registerViewCountUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken"),
					type: self.getPlayerType()
				},
				type: "POST"
			});
		}
		
		function registerLike(type, callback) {
			if (type !== "like" && type !== "dislike" && type !== "reset") {
				throw "Type must be 'like', 'dislike' or 'reset'.";
			}
			if (type === "like" && self.getLikeType() === "like" ||
				type === "dislike" && self.getLikeType() === "dislike" ||
				type === "reset" && self.getLikeType() === null) {
				// no change
				if (callback) {
					callback(true);
				}
				return;
			}
			var previousLikeType = self.getLikeType();
			jQuery.ajax(registerLikeUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken"),
					type: type
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					var success = data.success;
					if (success) {
						// like has changed on server. update cached versions accordingly
						if (previousLikeType === self.getLikeType()) {
							// make sure cache hasn't already been updated before this response was returned
							var likesChanged = false;
							var dislikesChanged = false;
							if (previousLikeType === null) {
								if (type === "like") {
									numLikes++;
									likesChanged = true;
								}
								else if (type === "dislike") {
									numDislikes++;
									dislikesChanged = true;
								}
							}
							else if (previousLikeType === "like") {
								if (type === "dislike") {
									numLikes--;
									likesChanged = true;
									numDislikes++;
									dislikesChanged = true;
								}
								else if (type === "reset") {
									numLikes--;
									likesChanged = true;
								}
							}
							else if (previousLikeType === "dislike") {
								if (type === "like") {
									numLikes++;
									likesChanged = true;
									numDislikes--;
									dislikesChanged = true;
								}
								else if (type === "reset") {
									numDisikes--;
									dislikesChanged = true;
								}
							}
							if (type === "like") {
								likeType = "like";
							}
							else if (type === "dislike") {
								likeType = "dislike";
							}
							else if (type === "reset") {
								likeType = null;
							}
							$(self).triggerHandler("likeTypeChanged");
							
							if (likesChanged) {
								$(self).triggerHandler("numLikesChanged");
							}
							if (dislikesChanged) {
								$(self).triggerHandler("numDisikesChanged");
							}
						}
					}
					if (callback) {
						callback(success);
					}
				}
				else {
					if (callback) {
						callback(false);
					}
				}
			});
		}
		
	};
	return PlayerController;
});
