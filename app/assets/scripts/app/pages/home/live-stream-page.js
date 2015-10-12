define([
	"jquery",
	"../../components/player-container",
	"../../page-data",
	"../../helpers/ajax-helpers",
	"../../helpers/pad",
	"../../synchronised-time",
	"lib/domReady!"
], function($, PlayerContainer, PageData, AjaxHelpers, pad, SynchronisedTime) {
	
	$(".page-live-stream").first().each(function() {
		
		var $pageContainer = $(this).first();

		$pageContainer.find(".player-container-component-container").each(function() {
			var self = this;
		
			var playerInfoUri = $(this).attr("data-info-uri");
			var registerWatchingUri = $(this).attr("data-register-watching-uri");
			var registerLikeUri = null;
			var enableAdminOverride = false;
			var loginRequiredMsg = $(this).attr("data-login-required-msg");
			var autoPlayVod = false; // should never be any
			var autoPlayStream = true;
			var vodPlayStartTime = null;
			var ignoreExternalStreamUrl = false;
			var initialVodQualityId = null;
			var initialStreamQualityId = null;
			var bottomBarMode = "full";
			var disableFullScreen = false;
			var placeQualitySelectionComponentInPlayer = false;
			var showTitleInPlayer = false;
			var embedded = false;
			var disablePlayerControls = false;
			var enableSmartAutoPlay = true;
		
			var playerContainer = new PlayerContainer(playerInfoUri, registerWatchingUri, registerLikeUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
			playerContainer.onLoaded(function() {
				$(self).empty();
				$(self).append(playerContainer.getEl());
			});
		});

		$pageContainer.find(".schedule-boxes").first().each(function() {

			var $playerContainerCol = $pageContainer.find(".player-container-col");
			var $container = $(this).first();
			var $title = $container.find(".title").first();
			var scheduleUri = $(this).attr("data-schedule-uri");

			var $prevLiveContainer = $container.find(".schedule-box-prev-live-container").first();
			var $liveContainer = $container.find(".schedule-box-live-container").first();
			var $comingUpContainer = $container.find(".schedule-box-coming-up-container").first();

			var scheduleBarVisible = false;

			var boxesInfo = [
				{
					name: "Coming Up",
					showTime: true,
					dataPropertyName: "comingUp",
					currentBox: null,
					queuedBox: null,
					$container: $comingUpContainer
				},
				{
					name: "Live Now",
					showTime: false,
					dataPropertyName: "live",
					currentBox: null,
					queuedBox: null,
					$container: $liveContainer
				},
				{
					name: "Previously",
					showTime: true,
					dataPropertyName: "previouslyLive",
					currentBox: null,
					queuedBox: null,
					$container: $prevLiveContainer
				}
			];

			setTimeout(makeRequest, 1500);


			function makeRequest() {
				$.ajax(scheduleUri, {
					cache: false,
					dataType: "json",
					headers: AjaxHelpers.getHeaders(),
					data: {
						csrf_token: PageData.get("csrfToken")
					},
					type: "POST"
				}).always(function(data, textStatus, jqXHR) {
					if (jqXHR.status === 200) {
						var now = SynchronisedTime.getDate().getTime();
						
						for(var i=0; i<boxesInfo.length; i++) {
							var boxInfo = boxesInfo[i];
							var boxData = data[boxInfo.dataPropertyName];
							var box = null;
							if (boxData) {
								box = new Box(boxInfo.name, boxInfo.showTime ? boxData.scheduledPublishTime : null, boxData.seriesName, boxData.name, boxData.coverArtUri, boxData.uri, boxInfo.$container);
								var publishDateObj = box.getDateObj();
								if (publishDateObj !== null && Math.abs(now - publishDateObj.getTime()) > 43200000) { // 12 hours
									// don't show boxes which are further than 12 hours away
									box = null;
								}
							}
							boxInfo.queuedBox = box;
						}
						
						animate(onComplete);
					}
					else {
						onComplete();
					}

					function onComplete() {
						// schedule again in 10 seconds
						setTimeout(makeRequest, 10000);
					}
				});
			}

			function animate(onComplete) {
				// set to true if everything needs to animate out and back in
				// to prevent items jumping
				var fullAnimationNeeded = false;
				var haveBoxes = false;
				var firstAnimation = false;
				for(var i=0; i<boxesInfo.length; i++) {
					var boxInfo = boxesInfo[i];
					var currentBox = boxInfo.currentBox;
					var queuedBox = boxInfo.queuedBox;
					if (queuedBox) {
						haveBoxes = true;
					}
					if (!queuedBox !== !currentBox) {
						// box getting added/removed
						fullAnimationNeeded = true;
					}
				}

				if (haveBoxes && !scheduleBarVisible) {
					$playerContainerCol.removeClass("col-md-12");
					$playerContainerCol.addClass("col-md-8");
					$container.removeClass("hidden-col");
					scheduleBarVisible = true;
					firstAnimation = true;
					setTimeout(runOutAnimations, 1100);
				}
				else {
					runOutAnimations();
				}

				function runOutAnimations() {
					var numOutAnimationsRunning = 0;
					if (!haveBoxes && scheduleBarVisible) {
						// schedule bar will animate out
						numOutAnimationsRunning++;
						$title.animate({
							opacity: 0
						}, 1300, function() {
							$title.addClass("hidden");
							onAnimateOutCompleted();
						});
					}
					for(var i=0; i<boxesInfo.length; i++) {
						var boxInfo = boxesInfo[i];
						var currentBox = boxInfo.currentBox;
						var queuedBox = boxInfo.queuedBox;
						
						numOutAnimationsRunning++;
						if (currentBox && (fullAnimationNeeded || !currentBox.equals(queuedBox))) {
							boxInfo.currentBox.animateOut(onAnimateOutCompleted);
						}
						else {
							setTimeout(onAnimateOutCompleted, 0);
						}
					}

					function onAnimateOutCompleted() {
						if (--numOutAnimationsRunning === 0) {
							runInAnimations();
						}
					}

					function runInAnimations() {
						var numInAnimationsRunning = 0;
						if (firstAnimation) {
							numInAnimationsRunning++;
							$title.removeClass("hidden");
							$title.animate({
								opacity: 1
							}, 800, onAnimateInCompleted);
						}
						for(var i=0; i<boxesInfo.length; i++) {
							var boxInfo = boxesInfo[i];
							var currentBox = boxInfo.currentBox;
							var queuedBox = boxInfo.queuedBox;

							numInAnimationsRunning++;
							if (currentBox && currentBox.isVisible()) {
								// the current box was not removed
								// therefore don't replace it
								setTimeout(onAnimateInCompleted, 0);
							}
							else {
								if (currentBox) {
									// remove old box from dom
									currentBox.remove();
								}
								if (queuedBox) {
									queuedBox.animateIn(onAnimateInCompleted);
								}
								else {
									setTimeout(onAnimateInCompleted, 0);
								}
								boxInfo.currentBox = queuedBox;
							}
						}

						function onAnimateInCompleted() {
							numInAnimationsRunning--;
							if (numInAnimationsRunning === 0) {
								onOutAnimationsComplete();
							}
							else if (numInAnimationsRunning < 0) {
								throw "numInAnimationsRunning should never go < 0";
							}
						
							function onOutAnimationsComplete() {
								if (!haveBoxes && scheduleBarVisible) {
									$container.addClass("hidden-col");
									$playerContainerCol.removeClass("col-md-8");
									$playerContainerCol.addClass("col-md-12");
									scheduleBarVisible = false;
									setTimeout(onComplete, 1100);
								}
								else {
									onComplete();
								}
							}
						}
					}
				}
			}


		
			function Box(name, time, showName, episodeName, coverArtUri, uri, $destination) {

				var formattedTime = null;
				var dateObj = null;
				if (time !== null) {
					dateObj = new Date(time*1000);
					formattedTime = pad(dateObj.getHours(), 2)+":"+pad(dateObj.getMinutes(), 2);
				}

				var $el = buildEl();
				var elInDom = false;
				var visible = false;

				this.animateIn = function(callback) {
					if (visible) {
						throw "Already animated in.";
					}
					$el.stop(true, true); // stop a previously running animation.
					$el.css("opacity", 0);
					if (!elInDom) {
						$destination.append($el);
						elInDom = true;
					}
					$el.animate({
						opacity: 1
					}, 800, function() {
						if (callback) {
							callback();
						}
					});
					visible = true;
				};

				this.animateOut = function(callback) {
					if (!visible) {
						throw "Not animated in.";
					}
					$el.stop(true, true); // stop a previously running animation.
					$el.animate({
						opacity: 0
					}, 1300, function() {
						if (callback) {
							callback();
						}
					});
					visible = false;
				};

				this.isVisible = function() {
					return visible;
				};

				this.remove = function() {
					if (visible) {
						throw "This box hasn't been animated out.";
					}

					if (elInDom) {
						$el.remove();
						elInDom = false;
					}
				};

				this.getName = function() {
					return name;
				};

				this.getDateObj = function() {
					return dateObj;
				};

				this.getTime = function() {
					return formattedTime;
				};

				this.getShowName = function() {
					return showName;
				};

				this.getEpisodeName = function() {
					return episodeName;
				};

				this.getCoverArtUri = function() {
					return coverArtUri;
				};

				this.getUri = function() {
					return uri;
				};

				// returns true if the provided box has the same content
				this.equals = function(box) {
					if (!box) {
						return false;
					}
					return (
						this.getName() === box.getName() && 
						this.getTime() === box.getTime() &&
						this.getShowName() === box.getShowName() &&
						this.getEpisodeName() === box.getEpisodeName() &&
						this.getCoverArtUri() === box.getCoverArtUri() &&
						this.getUri() === box.getUri()
					);
				};

				function buildEl() {
					var $box = $("<div />").addClass("schedule-box");
					if (!time) {
						$box.addClass("live-box");
					}
					var $window = $("<div />").addClass("embed-responsive embed-responsive-16by9 window").click(function() {
						window.location = uri;
					});
					var $artContainer = $("<div />").addClass("art-container");
					var $img = $("<img />").attr("src", coverArtUri);
					var $overlayTop = $("<div />").addClass("overlay overlay-top");
					var $name = $("<h2 />").addClass("box-name").text(name);
					var $time = null;
					if (time) {
						$time = $("<h4 />").addClass("box-time").text(formattedTime);
					}
					var $overlayBottom = $("<div />").addClass("overlay overlay-bottom");
					var $showName = null;
					if (showName) {
						$showName = $("<h3 />").addClass("box-show-name").text(showName);
					}
					var $episodeName = $("<div />").addClass("box-episode-name").text(episodeName);

					$box.append($window);
					$window.append($artContainer);
					$artContainer.append($img);
					$window.append($overlayTop);
					$overlayTop.append($name);
					if ($time) {
						$overlayTop.append($time);
					}
					$window.append($overlayBottom);
					if ($showName) {
						$overlayBottom.append($showName);
					}
					$overlayBottom.append($episodeName);
					return $box;
				};
			}

		});
		
	});

});