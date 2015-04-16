require.config({
	baseUrl: "/assets/scripts",
	paths: {
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

	require([
		"app/logger",
		"app/google-analytics",
		"app/version-log",
		"app/error-handler",
		"lib/bootstrap",
		"app/fit-text-handler",
		"app/synchronised-time",
		"app/video-js",
		"app/pages/embed/player-page"
	], function(logger, googleAnalytics) {
		// everything loaded
		logger.info("Embeddable player loaded.");
		googleAnalytics.registerModulesLoadTime("Embed", new Date().getTime() - startTime);
	});

})();