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
	"app/playlist",
	"app/synchronised-time",
	"app/video-js",
	"app/pages/home/player-page"
], function() {
	// everything loaded
});