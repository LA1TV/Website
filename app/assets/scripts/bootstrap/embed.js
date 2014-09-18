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
	"app/fit-text-handler",
	"app/player-container",
	"app/synchronised-time",
	"app/video-js",
], function() {
	// everything loaded
});