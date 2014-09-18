define([
	"jquery",
	"./page-data",
	"../lib/jquery.cookie"
], function($, PageData) {

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
	
	function updateTimeOffset() {
		var requestStartTime = new Date().getTime();
		$.ajax({
			url: PageData.get("baseUrl")+"/ajax/time",
			timeout: 3000,
			dataType: "json",
			data: {
				csrf_token: PageData.get("csrfToken")
			},
			cache: false,
			type: "POST"
		}).done(function(data, textStatus, jqXHR) {
			if (jqXHR.status === 200) {
				var currentTime = new Date().getTime();
				var roundTripTime = currentTime - requestStartTime;
				offsetTime = new Date(data.time*1000).getTime() - currentTime - (roundTripTime/2);
				updateCookieOffsetTime();
			}
		}).always(function() {
			var delay;
			if (updateCount <= 5) {
				delay = 3000;
				updateCount++;
			}
			else {
				delay = 55000;
			}
			setTimeout(updateTimeOffset, delay);
		});
	}
	
	function getOffsetTimeFromCookie() {
		if ($.cookie("synchronisedTimeOffset") !== null) {
			return parseInt($.cookie("synchronisedTimeOffset"));
		}
		return null;
	}
	
	function updateCookieOffsetTime() {
		$.cookie("synchronisedTimeOffset", ""+offsetTime)
	}
	
	// make the first request after a delay
	setTimeout(updateTimeOffset, 8000);
	
	return SynchronisedTime;
});