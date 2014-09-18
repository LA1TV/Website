require.config({
	baseUrl: "/assets/scripts",
	paths: {
		jquery: "lib/jquery",
		bootstrap: "lib/bootstrap"
	},
	shim: {
		bootstrap: ["jquery"]
	}
});

require([
	"bootstrap",
	"app/fit-text-handler",
	"app/player-container",
	"app/synchronised-time",
	"app/video-js",
	"app/pages/embed/player-page"
], function() {
	// everything loaded
});