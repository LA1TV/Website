var $ = require("jquery");
var Logger = require("app/logger");
require("app/pages/home/facebook-timeline.css");

$(document).ready(function() {

	// attach a facebook timeline to anything with this class
	$(".facebook-timeline-container").each(function() {
		var self = this;
		var pageUrl = $(this).attr("data-page-url");
		var height = $(this).attr("data-height");
		var showMessages = $(this).attr("data-show-messages") === "1";

		// load async (only if there is actually a timeline container on the page)
		require(["external/facebook"], function(fbLoader) {

			fbLoader.then(function(FB) {
				var tabs = "timeline";
				if (showMessages) {
					tabs += ", messages";
				}
				var $el = $("<div />").addClass("fb-page")
					.attr("data-href", pageUrl)
					.attr("data-height", height)
					.attr("data-width", "500") // max
					.attr("data-tabs", tabs)
					.attr("data-hide-cover", "false")
					.attr("data-show-facepile", "true")
					.attr("data-hide-cta", "true")
					.attr("data-small-header", "false")
					.attr("data-adapt-container-width", "true");
					$(self).append($el);
					// pass the el to facebook for it to render in
					FB.XFBML.parse($(self)[0]);
			}).catch(function() {
				Logger.info("Facebook script could not be loaded.");
			});
		});
	});
});