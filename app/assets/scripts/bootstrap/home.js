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
	"app/custom-accordian",
	"app/default-button-group",
	"app/fit-text-handler",
	"app/synchronised-time",
	"app/video-js",
	"app/jslink",
	"app/pages/home/player-page",
	"app/pages/home/playlist",
	"app/pages/home/promo-loader",
	"app/pages/home/home-page",
], function() {
	// everything loaded
});