define([
	"../../page-data",
], function(PageData) {
	
	// manages automatically moving on to the next item in a playlist when the current one ends,
	// depending what the mode is
	// mode: 0 = disabled, 1 = auto continue, 2 = auto continue and loop
	var AutoContinueManager = function(playerController, playlistInfoUri, mode) {
		
		this.getMode = function() {
			return mode;
		};
		
		this.setMode = function(newMode) {
			mode = newMode;
			onModeChanged();
		};
		
		var initialAutoPlayVod = playerController.getAutoPlayVod();
		var initialAutoPlayStream = playerController.getAutoPlayStream();
		var initialVodStartTime = playerController.getVodStartTime();
		
		onModeChanged();
		
		$(playerController).on("vodEnded streamStopped", function() {
			setTimeout(checkAndMoveOn, 0);
		});
		
		function onModeChanged() {
			if (mode !== 0) {
				playerController.setVodStartTime(0); // make sure the vod starts at the beginning
				playerController.setAutoPlayVod(true);
				playerController.setAutoPlayStream(true);
			}
			else {
				playerController.setVodStartTime(initialVodStartTime);
				playerController.setAutoPlayVod(initialAutoPlayVod);
				playerController.setAutoPlayStream(initialAutoPlayStream);
			}
		}
		
		// determine if should move onto something else, and do it if necessary
		var moveOnCheckInProgress = false;
		function checkAndMoveOn() {
			if (mode === 0) {
				// auto continue disabled
				return;
			}
			
			if (moveOnCheckInProgress) {
				// check is already ongoing so no point starting another.
				return;
			}
			
			if (!allowedToMoveOn()) {
				// try again in 8 seconds
				setTimeout(checkAndMoveOn, 8000);
				return;
			}
			moveOnCheckInProgress = true;
			// determine where to go next
			jQuery.ajax(playlistInfoUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken")
				},
				type: "POST"
			}).done(function(data) {
				if (!allowedToMoveOn()) {
					moveOnCheckInProgress = false;
					return;
				}
				var foundCurrentItem = false;
				var mediaItemToRedirectTo = null;
				for (var j=0; j===0 || (j<2 && mediaItemToRedirectTo === null && mode === 2); j++) {
					for (var i=0; i<data.length; i++) {
						var mediaItem = data[i];
						if (foundCurrentItem) {
							if ((mediaItem.vod !== null && mediaItem.vod.available) || (mediaItem.stream !== null && mediaItem.stream.state === 2)) {
								// has accessible vod, or stream which is live
								mediaItemToRedirectTo = mediaItem;
								break;
							}
						}
						if (mediaItem.id === playerController.getMediaItemId()) {
							foundCurrentItem = true;
						}
					}
					
					if (!foundCurrentItem) {
						// the current item has disappeared for some reason
						// pretend found it and run through the loop again to get first media item that is ready
						foundCurrentItem = true;
					}
				}
				if (mediaItemToRedirectTo === null) {
					moveOnCheckInProgress = false;
					// try again in 8 seconds
					setTimeout(checkAndMoveOn, 8000);
					return;
				}
				// redirect to next media item
				window.location = mediaItemToRedirectTo.url+"?autoContinueMode="+mode;
			}).fail(function() {
				moveOnCheckInProgress = false;
				// try again in 8 seconds
				setTimeout(checkAndMoveOn, 8000);
			});
		}
		
		// determine if we are allowed to move on
		var pageLoadTime = new Date().getTime();
		function allowedToMoveOn() {
			if (new Date().getTime() - pageLoadTime < 15000) {
				// less than 15 seconds have passed since the page loaded.
				// don't allow moving on yet to make sure don't start a dos attack!
				return false;
			}
			return mode !== 0 && !(playerController.getPlayerType() === "live" || (playerController.getPlayerType() === "vod" && !playerController.hasVodEnded()));
		}
	};
	
	return AutoContinueManager;
});