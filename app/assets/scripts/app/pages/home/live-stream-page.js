define([
	"jquery",
	"../../components/player-container",
	"lib/domReady!"
], function($, PlayerContainer) {
	
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

			var $prevLiveContainer = $container.find(".schedule-box-prev-live-container").first();
			var $liveContainer = $container.find(".schedule-box-live-container").first();
			var $comingUpContainer = $container.find(".schedule-box-coming-up-container").first();

			var $box = buildScheduleBox("Live", "12:20", "UniBrass 2016", "Episode 5 (Freshers Week Special)", "https://www.la1tv.co.uk/file/25125", "https://google.com");
			var $box2 = buildScheduleBox("Live", "12:20", "UniBrass 2016", "Episode 5 (Freshers Week Special)", "https://www.la1tv.co.uk/file/25125", "https://google.com");
			
			setTimeout(function() {
				animateIn($box, $liveContainer);
			}, 1000);
			
			setTimeout(function() {
				animateOut($box, function() {
					animateIn($box2, $liveContainer);
				});
			}, 4000);

			function animateIn($el, $destination, callback) {
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
			}

			function animateOut($el, callback) {
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
			}

			function buildScheduleBox(name, time, showName, episodeName, coverArtUrl, url) {
				var $box = $("<div />").addClass("schedule-box");
				var $window = $("<div />").addClass("embed-responsive embed-responsive-16by9 window").click(function() {
						window.location = url;
					});
				var $artContainer = $("<div />").addClass("art-container");
				var $img = $("<img />").attr("src", coverArtUrl);
				var $overlayTop = $("<div />").addClass("overlay overlay-top");
				var $name = $("<h2 />").addClass("box-name").text(name);
				var $time = $("<h4 />").addClass("box-time").text(time);
				var $overlayBottom = $("<div />").addClass("overlay overlay-bottom");
				var $showName = $("<h3 />").addClass("box-show-name").text(showName);
				var $episodeName = $("<div />").addClass("box-episode-name").text(episodeName);

				$box.append($window);
				$window.append($artContainer);
				$artContainer.append($img);
				$window.append($overlayTop);
				$overlayTop.append($name);
				$overlayTop.append($time);
				$window.append($overlayBottom)
				$overlayBottom.append($showName);
				$overlayBottom.append($episodeName);
				return $box;
			}

		});
		
	});

});