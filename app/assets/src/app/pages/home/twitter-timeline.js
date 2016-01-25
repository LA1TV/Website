var $ = require("jquery");
var twitterLoader = require("external/twitter");
var Logger = require("app/logger");
require("app/pages/home/twitter-timeline.css");

$(document).ready(function() {

	twitterLoader.then(function(twttr) {

		// attach a twitter timeline to anything with this class
		$(".twitter-timeline-container").each(function() {
			var self = this;
			var widgetId = $(this).attr("data-twitter-widget-id");
			var height = parseInt($(this).attr("data-twitter-widget-height"));
			
			var $timeline = $("<div />");
			$(this).append($timeline);
			twttr.widgets.createTimeline(widgetId, $timeline[0], {
				height: height,
				theme: "light"
			}).then(function(el) {
				// loaded
			});
		});
	}).catch(function() {
		Logger.info("Twitter script could not be loaded.");
	});
});