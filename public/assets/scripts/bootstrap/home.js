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
	"app/custom-accordian",
	"app/default-button-group",
	"app/fit-text-handler",
	"app/player-container",
	"app/playlist",
	"app/synchronised-time",
	"app/video-js",
	"app/pages/home/player-page"
], function() {
	// everything loaded
});