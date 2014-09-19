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
	"app/synchronised-time",
	"app/video-js",
	"app/pages/embed/player-page",
], function() {
	// everything loaded
});