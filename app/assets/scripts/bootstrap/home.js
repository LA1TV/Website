require.config({
	baseUrl: "/assets/scripts",
	paths: {
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics",
		videojs: "lib/video",
		twitter: "https://platform.twitter.com/widgets"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"lib/jquery.hotkeys": ["jquery"],
		"ga": {
			exports: "ga"
		},
		"lib/modernizr": {
			exports: "Modernizr"
		},
		"lib/videojs-markers": ["jquery", "videojs"],
		"lib/videojs-thumbnails": ["videojs"]
	}
});

(function() {
	var startTime = new Date().getTime();

	// load this quickly
	require([
		"lib/modernizr",
		"app/pages/home/side-banners"
	]);
	
	require([
		"app/logger",
		"app/google-analytics",
		"app/version-log",
		"app/error-handler",
		"lib/bootstrap",
		"app/custom-accordian",
		"app/default-button-group",
		"app/fit-text-handler",
		"app/synchronised-time",
		"app/video-js",
		"app/jslink",
		"app/confirmation-msg",
		"app/pages/home/player-page",
		"app/pages/home/account-page",
		"app/pages/home/playlist",
		"app/pages/home/promo-loader",
		"app/pages/home/home-page",
		"app/pages/home/twitter-timeline",
		"app/pages/home/cookie-compliance"
	], function(logger, googleAnalytics) {
		// everything loaded
		logger.info("Page loaded.");
		googleAnalytics.registerModulesLoadTime("Home", new Date().getTime() - startTime);
	});
	
})();