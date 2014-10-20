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

require([
	"lib/bootstrap",
	"app/google-analytics",
	"app/fit-text-handler",
	"app/synchronised-time",
	"app/video-js",
	"app/pages/embed/player-page",
], function() {
	// everything loaded
});