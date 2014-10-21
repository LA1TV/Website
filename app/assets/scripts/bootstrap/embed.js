require.config({
	baseUrl: "/assets/scripts",
	paths: {
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"ga": {
			exports: "ga"
		}
	}
});

(function() {
	var startTime = new Date().getTime();

	require([
		"app/google-analytics",
		"lib/bootstrap",
		"app/fit-text-handler",
		"app/synchronised-time",
		"app/video-js",
		"app/pages/embed/player-page",
	], function(googleAnalytics) {
		// everything loaded
		googleAnalytics.registerModulesLoadTime("Embed", new Date().getTime() - startTime);
	});

})();