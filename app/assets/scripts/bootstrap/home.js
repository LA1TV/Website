require.config({
	baseUrl: "/assets/scripts",
	paths: {
		jquery: "lib/jquery",
	},
	shim: {
		"lib/bootstrap": ["jquery"]
	}
});

require([
	"lib/bootstrap",
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