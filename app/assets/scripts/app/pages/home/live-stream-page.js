define([
	"jquery",
	"../../components/player-container",
	"../../page-data",
	"../../helpers/ajax-helpers",
	"../../helpers/pad",
	"lib/domReady!"
], function($, PlayerContainer, PageData, AjaxHelpers, pad) {
	
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

		$pageContainer.find(".schedule-boxes").first().each(function() {

			var $container = $(this).first();
			var scheduleUri = $(this).attr("data-schedule-uri");

			var $prevLiveContainer = $container.find(".schedule-box-prev-live-container").first();
			var $liveContainer = $container.find(".schedule-box-live-container").first();
			var $comingUpContainer = $container.find(".schedule-box-coming-up-container").first();

			var boxesInfo = [
				{
					name: "Coming Up",
					showTime: true,
					dataPropertyName: "comingUp",
					currentBox: null,
					$container: $comingUpContainer
				},
				{
					name: "Live Now",
					showTime: false,
					dataPropertyName: "live",
					currentBox: null,
					$container: $liveContainer
				},
				{
					name: "Previously",
					showTime: true,
					dataPropertyName: "previouslyLive",
					currentBox: null,
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
						var now = Date.now();
						for(var i=0; i<boxesInfo.length; i++) {
							var boxInfo = boxesInfo[i];
							var boxData = data[boxInfo.dataPropertyName];
							var box = null;
							if (boxData) {
								box = new Box(boxInfo.name, boxInfo.showTime ? boxData.scheduledPublishTime : null, boxData.seriesName, boxData.name, boxData.coverArtUri, boxData.uri);
								var publishDateObj = box.getDateObj();
								if (publishDateObj !== null && Math.abs(now - publishDateObj.getTime()) > 21600000) { // 6 hours
									// don't show boxes which are further than 6 hours away
									box = null;
								}
							}

							if ((!box && boxInfo.currentBox) || (box && !box.equals(boxInfo.currentBox))) {
								switchBox(box, boxInfo.$container, boxInfo.currentBox);
								boxInfo.currentBox = box;
							}
						}
					}

					// schedule again in 10 seconds
					setTimeout(makeRequest, 10000);
				});
			}


		
			function Box(name, time, showName, episodeName, coverArtUri, uri) {

				var formattedTime = null;
				var dateObj = null;
				if (time !== null) {
					dateObj = new Date(time*1000);
					formattedTime = pad(dateObj.getHours(), 2)+":"+pad(dateObj.getMinutes(), 2);
				}

				var $el = buildEl();
				var visible = false;

				this.animateIn = function($destination, callback) {
					if (visible) {
						throw "Already animated in.";
					}
					$el.stop(); // stop a previously running animation.
					$el.css("opacity", 0);
					$el.css("top", "-200px");
					$el.css("z-index", -1);
					$destination.append($el);
					$el.animate({
						top: 0,
						opacity: 1
					}, 500, function() {
						$el.css("z-index", "");
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
					$el.stop(); // stop a previously running animation.					
					$el.css("z-index", -1);
					$el.animate({
						top: "-200px",
						opacity: 0
					}, 500, function() {
						$el.remove();
						if (callback) {
							callback();
						}
					});
					visible = false;
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

			function switchBox(box, $destination, oldBox) {
				if (oldBox) {
					oldBox.animateOut(animateNewIn);
				}
				else {
					animateNewIn();
				}

				function animateNewIn() {
					box.animateIn($destination);
				}
			}

		});
		
	});

});