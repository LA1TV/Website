define([
	"jquery",
	"../fit-text-handler",
	"lib/video",
	"../synchronised-time",
	"../device-detection",
	"../helpers/nl2br",
	"../helpers/html-encode",
	"../helpers/pad",
	"lib/jquery.dateFormat",
	"../video-js"
], function($, FitTextHandler, videojs, SynchronisedTime, DeviceDetection, nl2br, e, pad) {
	
	var PlayerComponent = function(coverUri, responsive) {
		
		var self = this;
		
		this.getEl = function() {
			return $container;
		};
		
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
		
		this.setPlayerType = function(playerType) {
			if (playerType !== "live" && playerType !== "vod") {
				throw 'PlayerType must be "live" or "vod".';
			}
			queuedPlayerType = playerType;
			return this;
		};
		
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
		this.setPlayerStartTime = function(time, startPlaying) {
			queuedPlayerTime = time;
			queuedPlayerTimeStartPlaying = startPlaying ? true : false;
			return this;
		};
		
		this.render = function() {
			updateAd();
			updatePlayer();
			queuedPlayerTime = null;
			queuedPlayerTimeStartPlaying = null;
			return this;
		};
		
		this.destroy = function() {
			clearTimeout(updateAdTimerId);
			destroyAd();
			destroyPlayer();
			$container.remove();
		};
		
		this.getPlayerCurrentTime = function() {
			if (videoJsPlayer !== null) {
				return videoJsPlayer.currentTime();
			}
			return null;
		};
		
		this.play = function() {
			if (videoJsPlayer !== null) {
				videoJsPlayer.play();
			}
		};
		
		this.pause = function() {
			if (videoJsPlayer !== null) {
				videoJsPlayer.pause();
			}
		};
		
		this.paused = function() {
			if (videoJsPlayer !== null) {
				return videoJsPlayer.paused();
			}
			return null;
		};
		
		var showAd = null;
		var queuedShowAd = true;
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
		var currentAdTimeTxt = null;
		var currentAdLiveAtTxt = null;
		var videoJsLoadedMetadata = false;
		var playerType = null;
		var queuedPlayerType = null;
		var playerPreload = null;
		var queuedPlayerPreload = true;
		var showPlayer = null;
		var queuedShowPlayer = false;
		var queuedPlayerTime = null;
		var queuedPlayerTimeStartPlaying = null;
		var playerUris = null;
		var queuedPlayerUris = [];
		// id of timer that repeatedly calls updateAd() in order for countdown to work
		var updateAdTimerId = null;
		
		
		var $container = $("<div />").addClass("player-component embed-responsive");
		if (responsive) {
			$container.addClass("embed-responsive-16by9");
		}
		
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
		
		// contains reference to videojs player
		var videoJsPlayer = null;
		// reference to the dom element which contains the video tag
		var $player = null;
		
		
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
			
			if (queuedStartTime === null && startTime !== null) {
				// hiding start time
				$adLiveAt.hide().text("");
				currentAdLiveAtTxt = null;
				willBeLive = queuedWillBeLive = null;
				$adTime.hide().text("")
				currentAdTimeTxt = null;
			}
			else if (queuedStartTime !== null) {
				var currentDate = SynchronisedTime.getDate();
				var showCountdown = queuedStartTime.getTime() < currentDate.getTime() + 300000 && queuedStartTime.getTime() > currentDate.getTime();
				var timePassed = currentDate.getTime() >= queuedStartTime.getTime();
				
				var txt = null;
				if (!timePassed) {
					if (!showCountdown) {
						txt = $.format.date(queuedStartTime.getTime(), "HH:mm on D MMM yyyy");
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
			var $overlay = $("<div />").addClass("overlay");
			$adLiveAt = $("<div />").addClass("live-at-header fit-text txt-shadow").attr("data-compressor", "1.5").hide();
			$adStreamOver = $("<div />").addClass("stream-over-msg fit-text txt-shadow").attr("data-compressor", "2.8").text("This Stream Has Now Finished").hide();
			$adVodAvailableShortly = $("<div />").addClass("vod-available-shortly-msg fit-text txt-shadow").attr("data-compressor", "2.8").text("This Will Be Available To Watch On Demand Shortly").hide();
			$adTime = $("<div />").addClass("live-time fit-text txt-shadow").attr("data-compressor", "2.1").hide();
			$adCustomMsg = $("<div />").addClass("custom-msg fit-text txt-shadow").attr("data-compressor", "2.8").hide();
			$overlay.append($adLiveAt);
			$overlay.append($adStreamOver);
			$overlay.append($adVodAvailableShortly);
			$overlay.append($adTime);
			$overlay.append($adCustomMsg);
			$ad.append($bg);
			$ad.append($overlay);
			$container.append($ad);
		}
		
		function destroyAd() {
			if ($ad === null) {
				// ad doesn't exist
				return;
			}
			$ad.remove();
			$ad = null;
			startTime = null;
			willBeLive = null;
			customMsg = null;
			showStreamOver = null;
			showVodAvailableShortly = null;
			currentAdTimeTxt = null;
			currentAdLiveAtTxt = null;
		}
		
		// updates the player using the queued data.
		// creates/destroys the player if necessary
		function updatePlayer() {
			
			// determine if the player has to be reloaded or the settings can be applied in place.
			var reloadRequired = playerType !== queuedPlayerType || playerPreload !== queuedPlayerPreload || showPlayer !== queuedShowPlayer || havePlayerUrisChanged();
			
			// player needs reloading
			if (reloadRequired) {
				showPlayer = queuedShowPlayer;
				if (!showPlayer) {
					destroyPlayer();
				}
				else {
					createPlayer();
					playerType = queuedPlayerType;
				}
			}
			
			if (queuedPlayerTime !== null) {
				(function(startTime, startPlaying) {
					if (startPlaying) {
						videoJsPlayer.play();
					}
					onVideoJsLoadedMetadata(function() {	
						if (startTime > videoJsPlayer.duration()) {
							console.log("ERROR: The start time was set to a value which is longer than the length of the video. Not changing time.");
							return;
						}
						videoJsPlayer.currentTime(startTime);
					});
				})(queuedPlayerTime, queuedPlayerTimeStartPlaying);
			}
		}
		
		// creates the player
		// if the player already exists it destroys the current one first.
		function createPlayer() {
			// destroy current player if there is one
			destroyPlayer();
			
			$player = $("<div />").addClass("player embed-responsive-item");
			var $video = $("<video />").addClass("video-js vjs-default-skin").attr("poster", coverUri).attr("x-webkit-airplay", "allow");
			// disable browser context menu on video
			$video.on('contextmenu', function(e) {
				e.preventDefault();
			});
			
			// set the sources
			playerUris = queuedPlayerUris;
			for (var i=0; i<playerUris.length; i++) {
				var uri = playerUris[i];
				var supportedDevices = uri.supportedDevices;
				// if supportedDevices is null then that means all devices are supported. Otherwise only the devices listed are supported.
				if (supportedDevices !== null) {
					supportedDevices = supportedDevices.split(",");
				}
				var currentDevice = DeviceDetection.isMobile() ? "mobile" : "desktop";
				if (supportedDevices !== null && jQuery.inArray(currentDevice, supportedDevices) === -1) {
					// uri not supported on this device
					continue;
				}
				var $source = $("<source />").attr("type", uri.type).attr("src", uri.uri);
				$video.append($source);
			}

			$player.append($video);
			playerPreload = queuedPlayerPreload;
			videoJsPlayer = videojs($video[0], {
				width: "100%",
				height: "100%",
				controls: true,
				preload: playerPreload ? "auto" : "metadata",
				techOrder: ["html5", "flash"],
				autoPlayStartTime: false, // implementing autoPlayStartTime manually using callback
				poster: coverUri,
				loop: false
			}, function() {
				// called when player loaded.
				setTimeout(function() {
					// in timeout as needs videoJsPlayer needs to have been set before playerLoaded event.
					$(self).triggerHandler("playerLoaded");
				}, 0);
			});
			registerVideoJsEventHandlers();
			$container.append($player);
		}
		
		// removes the player
		function destroyPlayer() {
			if ($player === null) {
				// player doesn't exist.
				return;
			}
			videoJsPlayer.exitFullscreen();
			$(self).triggerHandler("playerDestroying");
			videoJsPlayer.dispose();
			videoJsPlayer = null;
			$player.remove();
			$player = null;
			playerPreload = null;
			playerUris = null;
			playerType = null;
			videoJsLoadedMetadata = false;
			$(self).triggerHandler("playerDestroyed");
		}
		
		function registerVideoJsEventHandlers() {
			videoJsPlayer.on("loadedmetadata", function() {
				videoJsLoadedMetadata = true;
			});
			
			videoJsPlayer.on("play", function() {
				$(self).triggerHandler("play");
			});
			videoJsPlayer.on("pause", function() {
				$(self).triggerHandler("pause");
			});
			videoJsPlayer.on("timeupdate", function() {
				$(self).triggerHandler("timeUpdate");
			});
			videoJsPlayer.on("ended", function() {
				videoJsPlayer.exitFullscreen();
				$(self).triggerHandler("ended");
			});
		}
		
		// executes callback when metadata has been loaded.
		// different to listening to event because will callback will always be executed even if event happened
		function onVideoJsLoadedMetadata(callback) {
			if (videoJsLoadedMetadata) {
				callback();
			}
			else {
				videoJsPlayer.one("loadedmetadata", function() {
					callback();
				});
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
		
	};
	
	return PlayerComponent;
});