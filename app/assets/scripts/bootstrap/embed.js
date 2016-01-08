require.config({
	baseUrl: "/assets/scripts",
	paths: {
		Clappr: "lib/clappr",
		optional: "app/requirejs-plugins/optional",
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"lib/jquery.hotkeys": ["jquery"],
		"ga": {
			exports: "ga"
		}
	}
});

(function() {
	var startTime = new Date().getTime();

	// modernizr is required before anything else because it adds the "js" class to the html tag.
	// Without this "js" class the page is in a div with display:none
	// this can cause issues with some things if not on the page when they try to render
	require([
		"lib/es6-promise",
		"lib/modernizr"
	], function(a) {
		a.polyfill();
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