define([
	"jquery",
	"../page-data",
	"../fit-text-handler",
	"../video-js",
	"lib/clappr",
	"../synchronised-time",
	"../helpers/nl2br",
	"../helpers/html-encode",
	"../helpers/pad",
	"lib/jquery.dateFormat"
], function($, PageData, FitTextHandler, videojs, Clappr, SynchronisedTime, nl2br, e, pad) {
	
	var PlayerComponent = function(coverUri, responsive, qualitySelectionComponent) {
	
		var self = this;
		
		this.getEl = function() {
			return $container;
		};
		
		// if set to null start time will be presumed as unknown
		this.setStartTime = function(startTime, willBeLiveParam) {
			queuedStartTime = startTime;
			queuedWillBeLive = willBeLiveParam;
			return this;
		};
		
		this.setCustomMsg = function(msg) {
			queuedCustomMsg = msg;
			return this;
		};
		
		this.showStreamOver = function(show, vodAvailable) {
			queuedShowStreamOver = show;
			return this;
		};
		
		this.showVodAvailableShortly = function(show) {
			queuedShowVodAvailableShortly = show;
			return this;
		};
		
		this.setTitle = function(title, linkUriCallback) {
			title = title === "" ? null : title;
			if (title === null && linkUriCallback !== null) {
				throw "If the title is null then the link uri callback must also be null.";
			}
			else if (title !== null && linkUriCallback === null) {
				throw "A link uri callback must be provided.";
			}
			queuedTitle = title;
			queuedTitleLinkUriCallback = linkUriCallback;
			return this;
		};
		
		// set the url to a page where this stream is being hosted.
		// if this is set the player will not load the stream.
		// Instead it will show the text "Live Now" when the stream is marked as live
		// and there will be a button at the top of the player which the user can click to
		// go to the url provided whilst the stream state is "not live" or "live".
		this.setExternalStreamUrl = function(url) {
			queuedExternalLiveStreamUrl = url;
			return this;
		};
		
		this.setPlayerType = function(playerType) {
			if (playerType !== "live" && playerType !== "vod") {
				throw 'PlayerType must be "live" or "vod".';
			}
			queuedPlayerType = playerType;
			return this;
		};
		
		// uris = array of {type, uri, uriWithDvrSupport}
		this.setPlayerUris = function(uris) {
			queuedPlayerUris = uris;
			return this;
		};
		
		this.setPlayerPreload = function(preload) {
			queuedPlayerPreload = preload;
			return this;
		};
		
		this.showPlayer = function(show) {
			queuedShowAd = !show;
			queuedShowPlayer = show;
			return this;
		};
	
		// set the player position to a certain time (in seconds) on the next render call.
		// if startPlaying is true the player will start playing if it is not already.
		// if roundTimeToSafeRegion is true then this means any values that are not between 5 seconds into the video and 10 seconds from the end will the time will get set to 0
		// the time and roundTimeToSafeRegion parameters only apply to VOD and will be interpreted as time 0
		this.setPlayerStartTime = function(time, startPlaying, roundTimeToSafeRegion) {
			queuedPlayerTime = time;
			queuedPlayerTimeStartPlaying = startPlaying ? true : false; // startPlaying could be undefined
			queuedPlayerRoundStartTimeToSafeRegion = roundTimeToSafeRegion ? true : false; // could be undefined
			return this;
		};
		
		// array of {time, title} (time is in seconds)
		// only applies to VOD
		this.setChapters = function(chapters) {
			queuedChapters = chapters;
			return this;
		};
		
		// array of {time, uri} (time is in seconds)
		// these will be applied when the video js player is created and not update automatically
		// because of how the plugin works.
		// this only applies to vod
		this.setScrubThumbnails = function(thumbnails) {
			queuedThumbnails = thumbnails;
			return this;
		};
		
		this.disableFullScreen = function(disable) {
			queuedDisableFullScreen = disable;
			return this;
		};
		
		// disables all player controls meaning the user cannot interact with the player.
		this.disableControls = function(disable) {
			queuedDisableControls = disable;
			return this;
		};
		
		this.render = function() {
			updateAd();
			updatePlayer();
			queuedPlayerTime = null;
			queuedPlayerTimeStartPlaying = null;
			queuedPlayerRoundStartTimeToSafeRegion = null;
			return this;
		};
		
		this.destroy = function() {
			clearTimeout(updateAdTimerId);
			destroyAd();
			destroyPlayer();
			$container.remove();
		};
		
		this.getPlayerCurrentTime = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					return videoJsPlayer.currentTime();
				}
			}
			return null;
		};
		
		this.getPlayerDuration = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					return videoJsPlayer.duration();
				}
			}
			return null;
		};
		
		// returns the error if an error has occurred with playback or null otherwise.
		this.getPlayerError = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					return videoJsPlayer.error();
				}
			}
			return null;
		};
		
		// returns true if the player has been shown and jumped to the correct time
		this.hasPlayerInitialized = function() {
			return playerInitialized;
		};
		
		// note when clappr is playing a live stream with dvr play appears to reset the play position to the live point
		this.play = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					videoJsPlayer.play();
				}
			}
			else if (playerType === "live") {
				if (clapprPlayer !== null) {
					clapprPlayer.play();
				}
			}
		}
		
		// TODO at the moment if clappr is playing a live stream without dvr, the stop() method needs to be
		// called instead of pause(). pause() will do nothing. Methods need adding to clappr to be able to check
		// if it has enabled dvr or not first.
		this.pause = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					videoJsPlayer.pause();
				}
			}
			else if (playerType === "live") {
				if (clapprPlayer !== null) {
					clapprPlayer.pause();
				}
			}
		};
		
		this.paused = function() {
			if (playerType === "vod") {
				if (videoJsPlayer !== null) {
					return videoJsPlayer.paused();
				}
			}
			else if (playerType === "live") {
				if (clapprPlayer !== null) {
					// not the clappr isPlaying represents the intended state, not what the current state may be
					// eg if the user clicks to seek to another part in the video the pause event will be fired,
					// then it will buffer, then the play event will be fired, but isPlaying will always be true
					// if the user was playing before they chose a new position
					return !clapprPlayer.isPlaying();
				}
			}
			return null;
		};
		
		this.hasEnded = function() {
			if (playerType !== "vod" || videoJsPlayer === null) {
				throw "Invalid operation.";
			}
			return videoJsPlayer.ended();
		};
		
		// jump to a specific time (seconds) in the video if it's vod
		// if startPlaying is true then it will start playing if it isn't currently
		this.jumpToTime = function(time, startPlaying) {
			if (videoJsPlayer !== null && playerType === "vod") {
				if (startPlaying) {
					// some mobile devices (iphones) will not allow javascript to start playback unless
					// it is triggered as a result of a user event. the play() call below  in the callback can become disconnected
					// from this execution path because it has to wait for the loaded metadata event. If this code
					// is executed as a result of a user event, then because this call is in the same execution path it
					// makes ios happy.
					videoJsPlayer.play();
				}
				onPlayerLoadedMetadata(function() {
					if (time > videoJsPlayer.duration()) {
						console.error("The time to jump to was set to a value which is longer than the length of the video.");
						return;
					}
					videoJsPlayer.currentTime(time);
					if (startPlaying) {
						videoJsPlayer.play();
					}
				});
			}
		};
		
		var showAd = null;
		var queuedShowAd = true;
		var showStartTime = false;
		var startTime = null;
		var queuedStartTime = null;
		var willBeLive = null;
		var queuedWillBeLive = null;
		var customMsg = null;
		var queuedCustomMsg = null;
		var showStreamOver = null;
		var queuedShowStreamOver = false;
		var showVodAvailableShortly = null;
		var queuedShowVodAvailableShortly = false;
		var title = null;
		var titleLinkUriCallback = null;
		var queuedTitle = null;
		var queuedTitleLinkUriCallback = null;
		var adExternalLiveStreamUrl = null;
		var externalStreamSlideExternalLiveStreamUrl = null;
		var queuedExternalLiveStreamUrl = null;
		var currentAdTimeTxt = null;
		var currentAdLiveAtTxt = null;
		var playerLoadedMetadata = false;
		var playerInitialized = false;
		var playerType = null;
		var queuedPlayerType = null;
		var playerPreload = null;
		var queuedPlayerPreload = true;
		var showPlayer = null;
		var queuedShowPlayer = false;
		var queuedPlayerTime = null;
		var queuedPlayerTimeStartPlaying = null;
		var disableFullScreen = null;
		var queuedDisableFullScreen = false;
		var disableControls = null;
		var queuedDisableControls = false;
		var chapters = [];
		var queuedChapters = [];
		// there is no 'thumbnails' because the plugin only allows the thumbnails to be applied on plugin initialisation
		var queuedThumbnails = [];
		var queuedPlayerRoundStartTimeToSafeRegion = null;
		var playerUris = null;
		var queuedPlayerUris = [];
		// id of timer that repeatedly calls updateAd() in order for countdown to work
		var updateAdTimerId = null;
		var wasFullScreen = null;
		var previousVolume = null;
		var wasMuted = null;
		
		var $container = $("<div />").addClass("player-component embed-responsive");
		if (responsive) {
			$container.addClass("embed-responsive-16by9");
		}
		
		// === AD ===
		// reference to dom element which holds the ad
		var $ad = null;
		var $adTitle = null;
		var $adStreamOver = null;
		var $adVodAvailableShortly = null;
		var $adCustomMsg = null;
		var $adLiveAt = null;
		var $adLiveIn = null;
		var $adTime = null;
		var $adCountdown = null;
		
		// === External Stream Slide ===
		// reference to dom element which holds the slide which is shown when a stream is live but at an external location
		var $externalStreamSlide = null;
		
		// === Shared Between AD and External Stream Slide ===
		var $overlayBottom = null;
		var $overlayTop = null;
		var $clickToWatchBtnContainer = null;
		var $clickToWatchBtn = null;
		
		// contains reference to videojs player which is used for vod
		var videoJsPlayer = null;
		// contains reference to the clappr player which is used for streams
		var clapprPlayer = null;
		// reference to the dom element which contains the video tag
		var $player = null;
		var $playerTopBarHeading = null;
		
		
		// this is necessary so that countdown is updated
		updateAdTimerId = setInterval(function() {
			updateAd();
		}, 200);
		
		// updates the ad using the queued data
		// creates/destroys the ad if necessary
		function updateAd() {
			if (queuedShowAd !== showAd) {
				if (queuedShowAd) {
					createAd();
				}
				else {
					destroyAd();
				}
				showAd = queuedShowAd;
			}
			if (!showAd) {
				// if the ad is currently not shown leave everything else queued for when it is.
				return;
			}
			
			if (queuedTitle !== title || queuedTitleLinkUriCallback !== titleLinkUriCallback) {
				title = queuedTitle;
				titleLinkUriCallback = queuedTitleLinkUriCallback;
				updatePlayerTitle();
			}
			
			// only show the start time if there is one set or if stream over message is not visible/going visible
			var queuedShowStartTime = true;
			if (queuedShowStreamOver) {
				// disable showing the time or external live stream url if stream over message visible
				queuedShowStartTime = false;
				queuedExternalLiveStreamUrl = null;
			}
			
			if (queuedExternalLiveStreamUrl !== adExternalLiveStreamUrl) {
				if (queuedExternalLiveStreamUrl !== null) {
					$clickToWatchBtn.attr("href", queuedExternalLiveStreamUrl);
					$clickToWatchBtnContainer.show();
				}
				else {
					$clickToWatchBtnContainer.hide();
				}
				adExternalLiveStreamUrl = queuedExternalLiveStreamUrl;
			}
			
			if (!queuedShowStartTime && showStartTime) {
				// hiding start time
				$adLiveAt.hide().text("");
				currentAdLiveAtTxt = null;
				willBeLive = queuedWillBeLive = null;
				$adTime.hide().text("");
				currentAdTimeTxt = null;
			}
			else if (queuedShowStartTime) {
				if (queuedStartTime === null && (startTime !== null || !showStartTime)) {
					// TODO show unavailable at the moment or something
					
					currentAdTimeTxt = null;
				}
				else if (queuedStartTime !== null) {
					var currentDate = SynchronisedTime.getDate();
					var tomorrowDate = new Date(currentDate.valueOf());
					tomorrowDate.setDate(currentDate.getDate()+1);
					var showCountdown = queuedStartTime.getTime() < currentDate.getTime() + 300000 && queuedStartTime.getTime() > currentDate.getTime();
					var timePassed = currentDate.getTime() >= queuedStartTime.getTime();
					
					var sameMonthAndYear = function(a, b) {
						return a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear();
					};
					
					var txt = null;
					if (!timePassed) {
						if (!showCountdown) {
							if (queuedStartTime.getDate() === currentDate.getDate() && sameMonthAndYear(queuedStartTime, currentDate)) {
								txt = "Today at "+$.format.date(queuedStartTime.getTime(), "HH:mm");
							}
							else if (queuedStartTime.getDate() === tomorrowDate.getDate() && sameMonthAndYear(queuedStartTime, tomorrowDate)) {
								txt = "Tomorrow at "+$.format.date(queuedStartTime.getTime(), "HH:mm");
							}
							else {
								txt = $.format.date(queuedStartTime.getTime(), "HH:mm on D MMM yyyy");
							}
						}
						else {
							var secondsToGo = Math.ceil((queuedStartTime.getTime() - currentDate.getTime()) / 1000);
							var minutes = Math.floor(secondsToGo/60);
							var seconds = secondsToGo%60;
							txt = minutes+" minute"+(minutes!==1?"s":"")+" "+pad(seconds, 2)+" second"+(seconds!==1?"s":"");
						}
					}
					
					willBeLive = queuedWillBeLive;
					
					var queuedAdLiveAtTxt = null;
					if (queuedWillBeLive) {
						queuedAdLiveAtTxt = "Live";
					}
					else {
						queuedAdLiveAtTxt = "Available";
					}
					
					if (!timePassed) {
						if (showCountdown) {
							queuedAdLiveAtTxt = queuedAdLiveAtTxt+" In";
						}
					}
					else {
						queuedAdLiveAtTxt = queuedAdLiveAtTxt+" Soon";
					}
					
					
					if (queuedAdLiveAtTxt !== currentAdLiveAtTxt) {
						$adLiveAt.text(queuedAdLiveAtTxt).show();
						FitTextHandler.register($adLiveAt);
						currentAdLiveAtTxt = queuedAdLiveAtTxt;
					}
					if (currentAdTimeTxt !== txt) {
						if (txt !== null) {
							$adTime.text(txt).show();
							FitTextHandler.register($adTime);
						}
						else {
							$adTime.hide().text("");
						}
						currentAdTimeTxt = txt;
					}
				}
			}
			showStartTime = queuedShowStartTime;
			startTime = queuedStartTime;

			if (customMsg !== queuedCustomMsg) {
				if (queuedCustomMsg === null) {
					$adCustomMsg.hide().text("");
				}
				else {
					$adCustomMsg.html(nl2br(e(queuedCustomMsg))).show();
					FitTextHandler.register($adCustomMsg);
				}
				customMsg = queuedCustomMsg;
			}
			if (showStreamOver !== queuedShowStreamOver) {
				if (queuedShowStreamOver) {
					$adStreamOver.show();
					FitTextHandler.register($adStreamOver);
				}
				else {
					$adStreamOver.hide();
				}
				showStreamOver = queuedShowStreamOver;
			}
			
			if (!queuedShowStreamOver) {
				// if the stream isn't marked as over disable vod available shortly message if set
				queuedShowVodAvailableShortly = false;
			}
			if (showVodAvailableShortly !== queuedShowVodAvailableShortly) {
				if (queuedShowVodAvailableShortly) {
					$adVodAvailableShortly.show();
					FitTextHandler.register($adVodAvailableShortly);
				}
				else {
					$adVodAvailableShortly.hide();
				}
				showVodAvailableShortly = queuedShowVodAvailableShortly;
			}
		}
		
		function createAd() {
			// destroy the ad first if necessary.
			// there should never be the case where this is called and it's already there but best be safe.
			destroyAd();
			$ad = $("<div />").addClass("ad embed-responsive-item");
			var $bg = $("<div />").addClass("bg");
			$bg.css("background-image", 'url("'+coverUri+'")'); // set the image uri. rest of background css is in css file
			$overlayTop = $("<div />").addClass("player-top-bar");
			createPlayerHeading();
			$overlayTop.append($playerTopBarHeading);
			createClickToWatchBtn(false);
			$overlayTop.append($clickToWatchBtnContainer);
			$overlayBottom = $("<div />").addClass("overlay overlay-bottom");
			createAdLiveAtText();
			$adStreamOver = $("<div />").addClass("stream-over-msg fit-text txt-shadow").attr("data-compressor", "2.8").text("This Stream Has Now Finished").hide();
			$adVodAvailableShortly = $("<div />").addClass("vod-available-shortly-msg fit-text txt-shadow").attr("data-compressor", "2.8").text("This Will Be Available To Watch On Demand Shortly").hide();
			$adTime = $("<div />").addClass("live-time fit-text txt-shadow").attr("data-compressor", "2.1").hide();
			$adCustomMsg = $("<div />").addClass("custom-msg fit-text txt-shadow").attr("data-compressor", "2.8").hide();
			$overlayBottom.append($adLiveAt);
			$overlayBottom.append($adStreamOver);
			$overlayBottom.append($adVodAvailableShortly);
			$overlayBottom.append($adTime);
			$overlayBottom.append($adCustomMsg);
			
			$ad.append($bg);
			$ad.append($overlayBottom);
			$ad.append($overlayTop);
			$container.append($ad);
		}
		
		function destroyAd() {
			if ($ad === null) {
				// ad doesn't exist
				return;
			}
			$ad.remove();
			$ad = null;
			adExternalLiveStreamUrl = null;
			showStartTime = false;
			startTime = null;
			willBeLive = null;
			customMsg = null;
			showStreamOver = null;
			showVodAvailableShortly = null;
			currentAdTimeTxt = null;
			currentAdLiveAtTxt = null;
			title = null;
		}
		
		function createExternalStreamSlide() {
			// destroy the external stream slide first if necessary.
			// there should never be the case where this is called and it's already there but best be safe.
			destroyExternalStreamSlide();
			$externalStreamSlide = $("<div />").addClass("ad embed-responsive-item");
			var $bg = $("<div />").addClass("bg");
			$bg.css("background-image", 'url("'+coverUri+'")'); // set the image uri. rest of background css is in css file
			$overlayTop = $("<div />").addClass("player-top-bar");
			createPlayerHeading();
			$overlayTop.append($playerTopBarHeading);
			createClickToWatchBtn(true);
			$overlayTop.append($clickToWatchBtnContainer);
			
			$overlayBottom = $("<div />").addClass("overlay overlay-bottom");
			createAdLiveAtText();
			$adLiveAt.text("Live Now!");
			$adLiveAt.show();
			$overlayBottom.append($adLiveAt);
			
			$externalStreamSlide.append($bg);
			$externalStreamSlide.append($overlayBottom);
			$externalStreamSlide.append($overlayTop);
			$container.append($externalStreamSlide);
			FitTextHandler.register($adLiveAt);
		}
		
		function destroyExternalStreamSlide() {
			if ($externalStreamSlide === null) {
				// external stream slide doesn't exist
				return;
			}
			$externalStreamSlide.remove();
			$externalStreamSlide = null;
			externalStreamSlideExternalLiveStreamUrl = null;
			title = null;
		}
		
		function createAdLiveAtText() {
			$adLiveAt = $("<div />").addClass("live-at-header fit-text txt-shadow").attr("data-compressor", "1.5").hide();
		}
		
		function createClickToWatchBtn(red) {
			$clickToWatchBtnContainer = $("<div />").addClass("click-to-watch-btn-container").hide();
			$clickToWatchBtn = $("<a />").addClass("btn "+(red?"btn-danger":"btn-primary")+" btn-block click-to-watch-btn").attr("target", "_blank").text("Click To Go To Live Stream Page");
			$clickToWatchBtnContainer.append($clickToWatchBtn);
		}
		
		// updates the player or external stream slide using the queued data.
		// creates/destroys the player or external stream slide if necessary
		// the external stream slide will be shown instead of the player when an external stream url is present and player type is "live"
		function updatePlayer() {
			// true if the external stream slide is currently visible
			var externalStreamSlideShown = playerType === "live" && externalStreamSlideExternalLiveStreamUrl !== null;
			// true if the external stream slide should be shown instead of the player
			var showExternalStreamSlide = queuedPlayerType === "live" && queuedExternalLiveStreamUrl !== null;
			
			// determine if the player has to be reloaded or the settings can be applied in place.
			var reloadRequired = playerType !== queuedPlayerType || showPlayer !== queuedShowPlayer || (!showExternalStreamSlide && (playerPreload !== queuedPlayerPreload || havePlayerUrisChanged() || disableControls !== queuedDisableControls)) || externalStreamSlideShown !== showExternalStreamSlide;
			
			// player needs reloading
			if (reloadRequired) {
				showPlayer = queuedShowPlayer;
				// destroy either the player or the external stream slide depending which is shown
				destroyExternalStreamSlide();
				externalStreamSlideShown = false;
				if (showPlayer) {
					// create either the player or external stream slide depending whether the external stream url is present
					if (showExternalStreamSlide) {
						destroyPlayer();
						playerType = queuedPlayerType;
						createExternalStreamSlide();
					}
					else {
						// destroy current player if there is one
						var playerExisted = destroyPlayer();
						playerType = queuedPlayerType;
						createPlayer(playerExisted);
					}
				}
				else {
					destroyPlayer();
				}
			}
			
			if (showExternalStreamSlide) {
				// update external stream slide
				// update title
				if (queuedTitle !== title  || queuedTitleLinkUriCallback !== titleLinkUriCallback) {
					title = queuedTitle;
					titleLinkUriCallback = queuedTitleLinkUriCallback;
					updatePlayerTitle();
				}
				
				// set the url on the button
				$clickToWatchBtn.attr("href", queuedExternalLiveStreamUrl);
				if (!externalStreamSlideShown) {
					// slide just been created, now show it
					$clickToWatchBtnContainer.show();
				}
				externalStreamSlideExternalLiveStreamUrl = queuedExternalLiveStreamUrl;
			}
			else if (showPlayer) {
				// update player
				if (queuedDisableFullScreen !== disableFullScreen) {
					updateFullScreenState();
				}
				
				if (queuedTitle !== title || queuedTitleLinkUriCallback !== titleLinkUriCallback) {
					title = queuedTitle;
					titleLinkUriCallback = queuedTitleLinkUriCallback;
					updatePlayerTitle();
				}
				
				// update the chapters
				if (haveChaptersChanged()) {
					chapters = queuedChapters;
					updateVideoJsMarkers();
				}
				
				// set the new time
				if (queuedPlayerTime !== null) {
					(function(startTime, startPlaying, roundToSafeRegion) {
						if (playerType === "vod") {
							onPlayerLoadedMetadata(function() {
								if (startTime > videoJsPlayer.duration()) {
									console.error("The start time was set to a value which is longer than the length of the video. Not changing time.");
									return;
								}
								else if (roundToSafeRegion) {
									if (startTime < 5 || startTime > videoJsPlayer.duration() - 10) {
										// set start time to 0 if it is not in the range from 5 seconds in to 10 seconds before the end.
										startTime = 0;
									}
								}
								if (startTime !== 0) {
									// if the time is 0 don't seek because seeking appears to replace the poster with the
									// frame that has been seeked to
									videoJsPlayer.currentTime(startTime);
								}
								if (startPlaying) {
									videoJsPlayer.play();
								}
								playerInitialized = true;
								$(self).triggerHandler("playerInitialized");
							});
						}
						else if (playerType === "live") {
							playerInitialized = true;
							$(self).triggerHandler("playerInitialized");
						}
					})(queuedPlayerTime, queuedPlayerTimeStartPlaying, queuedPlayerRoundStartTimeToSafeRegion);
				}
				else {
					if (playerType === "vod") {
						onPlayerLoadedMetadata(function() {
							playerInitialized = true;
							$(self).triggerHandler("playerInitialized");
						});
					}
					else if (playerType === "live") {
						playerInitialized = true;
						$(self).triggerHandler("playerInitialized");
					}
				}
			}
		}
		
		// creates the player
		// if the player already exists it destroys the current one first.
		function createPlayer(playerExisted) {
			$player = $("<div />").addClass("player embed-responsive-item");
			var $video = null;
			if (playerType === "vod") {
				$video = $("<video />").addClass("video-js vjs-default-skin").attr("poster", coverUri).attr("x-webkit-airplay", "allow");
				// disable browser context menu on video
				$video.on('contextmenu', function(e) {
					e.preventDefault();
				});
			}
			
			// set the sources
			playerUris = queuedPlayerUris;
			
			if (playerType === "vod") {
				// add sources that support dvr first so taken as preferred choice in browser
				for (var i=0; i<playerUris.length; i++) {
					var uri = playerUris[i];
					if (!uri.uriWithDvrSupport) {
						continue;
					}
					var $source = $("<source />").attr("type", uri.type).attr("src", uri.uri);
					$video.append($source);
				}
				
				// add sources that do not support dvr
				for (var i=0; i<playerUris.length; i++) {
					if (uri.uriWithDvrSupport) {
						continue;
					}
					var uri = playerUris[i];
					var $source = $("<source />").attr("type", uri.type).attr("src", uri.uri);
					$video.append($source);
				}
				$player.append($video);
			}
			
			playerPreload = queuedPlayerPreload;
			disableControls = queuedDisableControls;
			if (playerType === "vod") {
				videoJsPlayer = videojs($video[0], {
					width: "100%",
					height: "100%",
					controls: !disableControls,
					nativeControlsForTouch: true,
					preload: playerPreload ? "auto" : "metadata",
					techOrder: ["html5", "flash"],
					autoPlay: false, // implementing auto play manually using callback
					poster: coverUri,
					loop: false
				}, function() {
					// called when player loaded.
					if (qualitySelectionComponent !== null) {
						$player.find(".vjs-control-bar").each(function() {
							var $item = $("<div />").addClass("quality-selection-control").attr("tabindex", "0").attr("aria-live", "polite");
							$item.append(qualitySelectionComponent.getEl());
							$(this).append($item);
						});
					}
					
					setTimeout(function() {
						// in timeout as needs videoJsPlayer needs to have been set
						if (queuedThumbnails.length > 0) {
							var thumbnailsData = {};
							for(var i=0; i<queuedThumbnails.length; i++) {
								var a = queuedThumbnails[i];
								thumbnailsData[a.time] = {
									src: a.uri
								};
							}
							videoJsPlayer.thumbnails(thumbnailsData);
						}
						
						if (playerExisted) {
							// the player has just been destroyed before being recreated
							if (wasFullScreen) {
								// was previously full screen
								// make it full screen again
								// this may fail if the browser decides that this must be from a user interaction
								videoJsPlayer.requestFullscreen();
							}
							// set the volume and mute state back to what it was
							if (wasMuted !== null) {
								videoJsPlayer.muted(wasMuted);
							}
							if (previousVolume !== null) {
								videoJsPlayer.volume(previousVolume);
							}
							$(self).triggerHandler("playerLoaded");
						}
					}, 0);
				});
			}
			else if (playerType === "live") {
				// clappr can only take one uri with mime type so pick the first one with dvr,
				// or first one otherwise
				var chosenUri = playerUris[0];
				for (var i=0; i<playerUris.length; i++) {
					var uri = playerUris[i];
					if (!uri.uriWithDvrSupport) {
						continue;
					}
					chosenUri = uri;
					break;
				}
				
				var clapprOptions = {
					baseUrl: PageData.get("assetsBaseUrl")+"assets/clappr",
					width: "100%",
					height: "100%",
					poster: coverUri,
					preload: playerPreload ? "auto" : "metadata",
					persistConfig: false,
					loop: false,
					chromeless: disableControls,
					autoPlay: queuedPlayerTimeStartPlaying,
					mediacontrol: {seekbar: "#ff0000"}
				};
				
				if (chosenUri.uriWithDvrSupport) {
					// enable scrubbing when there is more than 20 seconds of content
					clapprOptions.hlsMinimumDvrSize = 20;
				}
				else {
					// the url shouldn't support dvr, but if for some reason it does require 3 minutes of contents
					clapprOptions.hlsMinimumDvrSize = 180;
				}
				
				clapprPlayer = new Clappr.Player(clapprOptions);
				clapprPlayer.attachTo($player[0]);
				clapprPlayer.load(chosenUri.uri, chosenUri.type);
				
				// TODO append qualitySelectionComponent somewhere if provided
			}
			
			updateFullScreenState();
			
			if (playerType === "vod") {
				// initialise markers plugin
				videoJsPlayer.markers({
					markerTip: {
						display: true,
						text: function(marker) {
							return marker.text;
						}
					},
					breakOverlay:{
						display: false
					},
					markerStyle: {
						width: '7px',
						'background-color': '#cccccc'
					},
					markers: []
				});
				updateVideoJsMarkers();
			}
			
			registerPlayerEventHandlers();
			
			var $topBar = $("<div />").addClass("player-top-bar");
			createPlayerHeading();
			$topBar.append($playerTopBarHeading);
			
			if (playerType === "vod") {
				$player.find(".video-js").append($topBar);
			}
			else if (playerType === "live") {
				// no top bar for live player (yet)
			}
			$container.append($player);
		}
		
		// removes the player
		// returns true if player destroyed or false if there wasn't one to destroy
		function destroyPlayer() {
			if ($player === null) {
				// player doesn't exist.
				return false;
			}
			if (playerType === "vod") {
				wasFullScreen = videoJsPlayer.isFullscreen();
				wasMuted = videoJsPlayer.muted();
				previousVolume = videoJsPlayer.volume();
				videoJsPlayer.exitFullscreen();
			}
			else if (playerType === "live") {
				wasFullScreen = null; // no api call to get this
				wasMuted = null; // no api call to get this
				previousVolume =  null; // no api call to get this
			}
			$(self).triggerHandler("playerDestroying");
			if (playerType === "vod") {
				videoJsPlayer.dispose();
				videoJsPlayer = null;
			}
			else if (playerType === "live") {
				clapprPlayer.destroy();
				clapprPlayer = null;
			}
			$player.remove();
			$player = null;
			playerPreload = null;
			playerControls = null;
			playerUris = null;
			playerType = null;
			title = null;
			playerLoadedMetadata = false;
			playerInitialized = false;
			$(self).triggerHandler("playerDestroyed");
			return true;
		}
		
		function registerPlayerEventHandlers() {
			var onLoadedMetadata = function() {
				playerLoadedMetadata = true;
				$(self).triggerHandler("loadedMetadata");
			};
			var onPlay = function() {
				$(self).triggerHandler("play");
			};
			var onPause = function() {
				$(self).triggerHandler("pause");
			};
			var onTimeUpdate = function() {
				$(self).triggerHandler("timeUpdate");
			};
			var onEnded = function() {
				if (playerType === "vod") {
					videoJsPlayer.exitFullscreen();
				}
				$(self).triggerHandler("ended");
			};
			
			if (playerType === "vod") {
				videoJsPlayer.on("loadedmetadata", onLoadedMetadata);
				videoJsPlayer.on("play", onPlay);
				videoJsPlayer.on("pause", onPause);
				videoJsPlayer.on("timeupdate", onTimeUpdate);
				videoJsPlayer.on("ended", onEnded);
			}
			else if (playerType === "live") {
				// live player never fires loadedmetadata event
				clapprPlayer.on(Clappr.Events.PLAYER_PLAY, onPlay);
				clapprPlayer.on(Clappr.Events.PLAYER_PAUSE, onPause);
				clapprPlayer.on(Clappr.Events.PLAYER_TIMEUPDATE, onTimeUpdate);
				clapprPlayer.on(Clappr.Events.PLAYER_ENDED, onEnded);
			}
		}
		
		// executes callback when metadata has been loaded.
		// different to listening to event because will callback will always be executed even if event happened
		function onPlayerLoadedMetadata(callback) {
			if (playerType === "live") {
				throw "Live player does not have a loaded metadata event.";
			}
			
			if (playerLoadedMetadata) {
				callback();
			}
			else {
				if (playerType === "vod") {
					videoJsPlayer.one("loadedmetadata", function() {
						callback();
					});
				}
				else if (playerType === "live") {
					clapprPlayer.one(Clappr.Events.PLAYBACK_LOADEDMETADATA, function() {
						callback();
					});
				}
			}
		}
		
		function createPlayerHeading() {
			$playerTopBarHeading = $("<div />").addClass("heading").text("");
			$playerTopBarHeading.click(function() {
				if (titleLinkUriCallback !== null) {
					self.pause(); // pause if something is playing
					// get the url from the callback
					window.open(titleLinkUriCallback(), "_blank");
				}
			});
			$playerTopBarHeading.hide();
			updatePlayerTitle();
		}
		
		function updateFullScreenState() {
			if (queuedDisableFullScreen) {
				$player.attr("data-full-screen-enabled", "0");
				if (playerType === "vod") {
					videoJsPlayer.exitFullscreen();
				}
				else if (playerType === "live") {
					// hopefully clappr will not start in full screen
				}
			}
			else {
				$player.attr("data-full-screen-enabled", "1");
			}
			disableFullScreen = queuedDisableFullScreen;
		}
		
		function updateVideoJsMarkers() {
			var markers = [];
			for (var i=0; i<chapters.length; i++) {
				var chapter = chapters[i];
				markers.push({
					time: chapter.time,
					text: chapter.title
				});
			}
			onPlayerLoadedMetadata(function() {
				videoJsPlayer.markers.reset(markers);
			});
		}
		
		function updatePlayerTitle() {
			$playerTopBarHeading.text(title !== null ? title : "");
			if (title !== null) {
				$playerTopBarHeading.show();
			}
			else {
				$playerTopBarHeading.hide();
			}
		}
		
		function havePlayerUrisChanged() {
			if ((queuedPlayerUris === null && playerUris !== null) || (queuedPlayerUris !== null && playerUris === null)) {
				return true;
			}
			
			if (playerUris.length !== queuedPlayerUris.length) {
				return true;
			}
			
			for (var i=0; i<queuedPlayerUris.length; i++) {
				var queuedUri = queuedPlayerUris[i];
				var uri = playerUris[i];
				if (uri.uri !== queuedUri.uri || uri.type !== queuedUri.type) {
					return true;
				}
			}
			return false;
		}
		
		function haveChaptersChanged() {
			if (queuedChapters.length !== chapters.length) {
				return true;
			}
			
			for (var i=0; i<queuedChapters.length; i++) {
				var queuedChapter = queuedChapters[i];
				var chapter = chapters[i];
				if (queuedChapter.time !== chapter.time || queuedChapter.title !== chapter.title) {
					return true;
				}
			}
			return false;
		}
		
	};
	
	return PlayerComponent;
});