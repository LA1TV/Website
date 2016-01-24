define([
	"jquery",
	"./page-data",
	"./cookie-config",
	"./notification-service",
	"imports?jQuery=lib/jquery!lib/jquery.cookie"
], function($, PageData, cookieConfig, notificationService) {

	var SynchronisedTime = null;
	// manage synchronised time
	
	var offsetTime = getOffsetTimeFromCookie() || 0 // no of milliseconds out compared with local time
	var updateCount = 0;
	
	SynchronisedTime = {
		getDate: function() {
			return new Date(new Date().getTime() + offsetTime);
		},
		getOffset: function() {
			return offsetTime;
		}
	};

	notificationService.on("synchronisedClock.time", function(time) {
		updateTimeOffset(time);
	});

	function updateTimeOffset(serverTime) {
		var currentTime = new Date().getTime();
		offsetTime = serverTime - currentTime;
		updateCookieOffsetTime();
	}

	function getOffsetTimeFromCookie() {
		if ($.cookie("synchronisedTimeOffset") !== null) {
			return parseInt($.cookie("synchronisedTimeOffset"));
		}
		return null;
	}
	
	function updateCookieOffsetTime() {
		$.cookie("synchronisedTimeOffset", ""+offsetTime, cookieConfig)
	}

	return SynchronisedTime;
});