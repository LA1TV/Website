require.config({
	baseUrl: "/assets/scripts",
	paths: {
		optional: "app/requirejs-plugins/optional",
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics",
		videojs: "lib/video"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"lib/jquery.hotkeys": ["jquery"],
		"ga": {
			exports: "ga"
		},
		"lib/videojs-markers": ["jquery", "videojs"],
		"lib/videojs-thumbnails": ["videojs"]
	}
});

(function() {
	var startTime = new Date().getTime();

	// modernizr is required before anything else because it adds the "js" class to the html tag.
	// Without this "js" class the page is in a div with display:none
	// this can cause issues with some things if not on the page when they try to render
	require([
		"lib/modernizr",
	], function() {
		require([
			"app/logger",
			"app/google-analytics",
			"app/version-log",
			"app/error-handler",
			"lib/bootstrap",
			"app/fit-text-handler",
			"app/synchronised-time",
			"app/pages/embed/player-page"
		], function(logger, googleAnalytics) {
			// everything loaded
			logger.info("Embeddable player loaded.");
			googleAnalytics.registerModulesLoadTime("Embed", new Date().getTime() - startTime);
		});
	});

})();