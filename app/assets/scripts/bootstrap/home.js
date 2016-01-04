require.config({
	baseUrl: "/assets/scripts",
	paths: {
		Clappr: "lib/clappr",
		optional: "app/requirejs-plugins/optional",
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics",
		twitter: "https://platform.twitter.com/widgets",
		moxie: "lib/moxie",
		plupload: "lib/plupload"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"lib/jquery.hotkeys": ["jquery"],
		"lib/jquery.flexslider": ["jquery"],
		"lib/jquery.visible": ["jquery"],
		"ga": {
			exports: "ga"
		},
		"moxie": {
			exports: "mOxie"
		},
		"plupload": {
			exports: "plupload",
			deps: ['moxie']
		},
		"lib/modernizr": {
			exports: "Modernizr"
		}
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
		require(["app/pages/home/side-banners"]); // load quickly
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
			"app/jslink",
			"app/confirmation-msg",
			"app/pages/home/search",
			"app/pages/home/player-page",
			"app/pages/home/live-stream-page",
			"app/pages/home/account-page",
			"app/pages/home/playlist",
			"app/pages/home/promo-loader",
			"app/pages/home/browser-notification-manager",
			"app/pages/home/home-page",
			"app/pages/home/twitter-timeline",
			"app/pages/home/cookie-compliance"
		], function(logger, googleAnalytics) {
			// everything loaded
			logger.info("Page loaded.");
			googleAnalytics.registerModulesLoadTime("Home", new Date().getTime() - startTime);
		});
		
	});
		
})();