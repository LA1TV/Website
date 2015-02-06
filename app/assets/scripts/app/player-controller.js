// takes in a Player Component and handles all communication with it.
// has knowledge of the requests to the server and the responses returned

// communicates with a PlayerControllerQualitiesHander to manage quality selection.
// also manages likes and view count

define([
	"jquery",
	"./components/player",
	"./page-data",
	"./synchronised-time",
	"lib/domReady!"
], function($, PlayerComponent, PageData, SynchronisedTime) {
	var PlayerController = null;

	// qualities handler needs to be an object with the following methods:
	// - getChosenQualityId()
	// 		should return the current chosen quality id
	// - setAvailableQualities(qualities)
	//		called with an array of {id, name}
	//		will be an empty array in the case of there being no video
	
	PlayerController = function(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeUri, qualitiesHandler, responsive, autoPlay, ignoreExternalStreamUrl) {
		
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
		
		// total number of likes or null if like total is disabled.
		this.getNumLikes = function() {
			return numLikes;
		};
		
		// total number of dislikes or null if dislike total is disabled
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
				var streamCount = self.getStreamViewCount();
				var vodCount = self.getVodViewCount();
				if (streamCount === null && vodCount === null) {
					return null;
				}
				
				var count = 0;
				if (streamCount !== null) {
					count += streamCount;
				}
				if (vodCount !== null) {
					count += vodCount;
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
		};
		
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
		var vodSourceId = null;
		var vodRememberedStartTime = null;
		var vodViewCount = null;
		var streamViewCount = null;
		var numLikes = null;
		var numDislikes = null;
		var likeType = null; // "like", "dislike" or null
		var streamState = null;
		var streamStartTime = null; // the time the user started watching the stream
		var overrideModeEnabled = null;
		var queuedOverrideModeEnabled = false;
		var embedData = null;
		var viewCountRegistered = false;
		var rememberedTimeTimerId = null;
		
		
		$(qualitiesHandler).on("chosenQualityChanged", function() {
			updatePlayer();
		});
		
		// kick it off
		update();
		
		function update() {
		
			var onComplete = function() {
				if (!destroyed) {
					// schedule update again in 15 seconds
					timerId = setTimeout(update, 15000);
				}
			};
		
			jQuery.ajax(playerInfoUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken")
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					
					var callback = function(time) {
						cachedData = data;
						vodSourceId = data.vodSourceId;
						vodRememberedStartTime = time;
						render();
						onComplete();
					};
				
					if (data.vodSourceId !== null) {
						getRememberedTime(data, callback);
					}
					else {
						callback();
					}
				}
				else {
					onComplete();
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
			var data = cachedData;
			
			var firstLoad = false;
			if (playerComponent === null) {
				firstLoad = true;
				playerComponent = new PlayerComponent(data.coverUri, responsive);
				$(self).triggerHandler("playerComponentElAvailable");
				$(playerComponent).on("play", function() {
					if (!viewCountRegistered && !overrideModeEnabled) {
						// register view count first time play occurs if user not in admin override mode
						viewCountRegistered = true;
						registerViewCount();
					}
				});
				$(playerComponent).on("loadedMetadata", function() {
					// called at the point when the browser starts receiving the stream/video
					// update the stream start time if it is a live stream
					if (playerType === "live") {
						streamStartTime = SynchronisedTime.getDate();
					}
				});
			}
			
			var externalStreamUrl = data.hasStream && !ignoreExternalStreamUrl ? data.externalStreamUrl : null;
			var queuedPlayerType = "ad";
			// live streams take precedence over vod
			if (data.hasStream && (data.streamState === 2 || (overrideModeEnabled && data.streamState === 1))) {
				if (externalStreamUrl !== null || data.streamUris.length > 0) {
					queuedPlayerType = "live";
				}
			}
			else if (data.hasVod && (data.vodLive && (!data.hasStream || data.streamState !== 1)) || overrideModeEnabled) {
				if (data.videoUris.length > 0) {
					queuedPlayerType = "vod";
					externalStreamUrl = null;
				}
			}
			
			// if the player type is currently live but going to ad, and the current stream state is "show over", and the stream is in the player, not on an external page, and there are still stream uris
			if (playerType === "live" && queuedPlayerType === "ad" && data.hasStream && data.streamState === 3 && externalStreamUrl === null && data.streamUris.length > 0) {
				// see if the user has gone past the point in their local version of the stream when the show was marked as over
				// if they haven't then leave their player as "live" until this happens, or there is a videojs error.
				if (playerComponent.getPlayerError() === null && streamStartTime !== null && data.streamEndTime !== null && (streamStartTime.getTime()/1000) + playerComponent.getPlayerCurrentTime() < data.streamEndTime) {
					// keep as "live"
					queuedPlayerType = "live";
				}
			}
			
			var uriGroups = [];
			if (externalStreamUrl === null) {
				// the stream is being hosted in the player, or it's not a stream or ad
				if (queuedPlayerType === "live") {
					uriGroups = data.streamUris;
				}
				else if (queuedPlayerType === "vod") {
					uriGroups = data.videoUris;
				}
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
					var autoPlayStartTime = vodRememberedStartTime !== null ? vodRememberedStartTime : 0;
					
					if (autoPlay && firstLoad) {
						// this is the first load of the player, and the autoplay flag is set, so autoplay
						// the second param means reset the time to 0 if it doesn't makes sense. E.g if the time is within the last 10 seconds of the video or < 5.
						playerComponent.setPlayerStartTime(autoPlayStartTime, true, true);
					}
					else if (!urisChanged) {
						// set the start time to the time the user was previously at.
						// the second param means reset the time to 0 if it doesn't makes sense. E.g if the time is within the last 10 seconds of the video or < 5.
						playerComponent.setPlayerStartTime(autoPlayStartTime, false, true);
					}
					else if (urisChanged) {
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
			playerComponent.setExternalStreamUrl(externalStreamUrl);
			
			if (queuedPlayerType === "vod") {
				// start updating the local database with the users position in the video.
				startRememberedTimeUpdateTimer();
			}
			else {
				stopRememberedTimeUpdateTimer();
			}
			
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
		
		function startRememberedTimeUpdateTimer() {
			if (rememberedTimeTimerId !== null) {
				// timer already running
				return;
			}
			var fn = function() {
				updateRememberedTime();
			};
			setTimeout(fn, 0); // run immediately as well as every 5 seconds
			rememberedTimeTimerId = setInterval(fn, 5000);
		}
		
		function stopRememberedTimeUpdateTimer() {
			if (rememberedTimeTimerId === null) {
				// timer isn't running
				return;
			}
			clearInterval(rememberedTimeTimerId);
			rememberedTimeTimerId = null;
		}
		
		function updateRememberedTime() {
			if (!areRememberedTimeUpdateConditionsMet()) {
				return;
			}
			
			updateRememberedTimeInDb();
			updateRememberedTimeOnServer();
		}
		
		// get the time that the user was last up to in the vod (via a callback)
		// requires the latest version of the player info data from the response.
		function getRememberedTime(data, callback) {
			if (data.vodSourceId === null) {
				callback(null);
				return;
			}
			
			// first see if there is a remembered time in the servers info response
			if (data.rememberedPlaybackTime !== null) {
				callback(data.rememberedPlaybackTime);
			}
			else {
				// could not get time from server. return the local one instead (or null)
				getRememberedTimeFromDb(data.vodSourceId, function(result) {
					callback(result);
				});
			}
		}
		
		function updateRememberedTimeInDb() {
			if (!window.indexedDB) {
				// browser does not have indexedDB support so do nothing
				return;
			}
		
			// store the current time into the vod in an object store using the vodSourceId as the identifier.
			// vodSourceId is the id of the source file that the different qualities of the video were generated from
			try {
				var request = createOpenPlaybackTimesDatabaseRequest();
				request.onsuccess = function(event) {
					var db = event.target.result;
					var transaction = db.transaction(["playback-times"], "readwrite");
					transaction.oncomplete = function(event) {
						// success
					};
					
					transaction.onerror = function(event) {
						console.error("Error when trying to update \"playback-times\"  object store.");
					};
					
					var objectStore = transaction.objectStore("playback-times");
					
					// first remove any old entries. (Entries older than 3 weeks)
					var cutoffTime = new Date().getTime() - (21 * 24 * 60 * 60 * 1000);
					objectStore.index("timeUpdated").openKeyCursor(IDBKeyRange.upperBound(cutoffTime, true)).onsuccess = function(event) {
						var cursor = event.target.result;
						if (cursor) {
							objectStore.delete(cursor.primaryKey);
							cursor.continue();
						}
					};
					
					// only update the time whilst the video is actually playing. This means if the user has the video open in several tabs the time will be updated for the one they are watching
					if (areRememberedTimeUpdateConditionsMet()) {	
						var request = objectStore.put({
							id: vodSourceId,
							time: playerComponent.getPlayerCurrentTime(),
							timeUpdated: new Date().getTime()
						});
						request.onerror = function(event) {
							console.error("Error when trying to create/update object in \"playback-times\"  object store.");
						};
					}
				};
			}
			catch(e) {
				console.error("Exception thrown when trying to read from \"playback-times\" object store.");
				callback(null);
			}
		}
		
		function updateRememberedTimeOnServer() {
			if (!PageData.get("loggedIn")) {
				// don't bother making the request if the user is not logged in.
				return;
			}
			
			// make request to update time on server.
			jQuery.ajax(updatePlaybackTimeUri+"/"+vodSourceId, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken"),
					time: playerComponent.getPlayerCurrentTime()
				},
				type: "POST"
			});
		}
		
		// get the time the user was up to in the current video last time they watched it.
		// callback should take 1 param which will be the time or null if time could not be retrieved.
		// id is the id of the source file
		function getRememberedTimeFromDb(id, callback) {
			
			if (!window.indexedDB) {
				// browser does not have indexedDB support so do nothing
				callback(null);
				return;
			}
			
			try {
				var request = createOpenPlaybackTimesDatabaseRequest(function() {
					// error connecting to database
					callback(null);
				});
				
				request.onsuccess = function(event) {
					var db = event.target.result;
					var transaction = db.transaction(["playback-times"]);
					transaction.oncomplete = function(event) {
						// success
					};
					
					transaction.onerror = function(event) {
						console.error("Error when trying to read from \"playback-times\" object store.");
						callback(null);
					};
					
					var objectStore = transaction.objectStore("playback-times");
					var resultRequest = objectStore.get(id);
					
					resultRequest.onerror = function(event) {
						console.error("Error when trying to request the playback time from the \"playback-times\" object store.");
						callback(null);
					};
					
					resultRequest.onsuccess = function(event) {
						var result = resultRequest.result;
						callback(result ? result.time : null);
					};
					
				};
			}
			catch(e) {
				console.error("Exception thrown when trying to read from \"playback-times\" object store.");
				callback(null);
			}
		}
		
		function areRememberedTimeUpdateConditionsMet() {
			return !(playerType !== "vod" || vodSourceId === null || playerComponent === null || playerComponent.paused() || playerComponent.getPlayerCurrentTime() == null);
		}
		
		function createOpenPlaybackTimesDatabaseRequest(onErrorCallback) {
			try {
				// open/create "PlaybackTimes" database
				var request = window.indexedDB.open("PlaybackTimes", 6);
				request.onerror = function(event) {
					console.error("Error occurred when trying to create/open \"PlaybackTimes\" database.");
					if (onErrorCallback) {
						onErrorCallback(event);
					}
				};
				request.onupgradeneeded = function(event) {
					var db = event.target.result;
					// Create an objectStore for this database
					if (db.objectStoreNames.contains("playback-times")) {
						db.deleteObjectStore("playback-times"); // remove old version first
					}
					var objectStore = db.createObjectStore("playback-times", { keyPath: "id" });
					objectStore.createIndex("timeUpdated", "timeUpdated", { unique: false });
				};
				return request;
			}
			catch(e) {
				console.error("Exception occurred when trying to open/create the \"PlaybackTimes\" database.");
				onErrorCallback(null);
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
									if (numLikes !== null) numLikes++;
									likesChanged = true;
								}
								else if (type === "dislike") {
									if (numDislikes !== null) numDislikes++;
									dislikesChanged = true;
								}
							}
							else if (previousLikeType === "like") {
								if (type === "dislike") {
									if (numLikes !== null) numLikes--;
									likesChanged = true;
									if (numDislikes !== null) numDislikes++;
									dislikesChanged = true;
								}
								else if (type === "reset") {
									if (numLikes !== null) numLikes--;
									likesChanged = true;
								}
							}
							else if (previousLikeType === "dislike") {
								if (type === "like") {
									if (numLikes !== null) numLikes++;
									likesChanged = true;
									if (numDislikes !== null) numDislikes--;
									dislikesChanged = true;
								}
								else if (type === "reset") {
									if (numDislikes !== null) numDisikes--;
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
