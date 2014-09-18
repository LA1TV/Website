require.config({
	baseUrl: "/scripts",
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
	"app/device-detection",
	"app/fit-text-handler",
	"app/page-data",
	"app/player-container",
	"app/player-page",
	"app/playlist",
	"app/synchronised-time",
	"app/video-js"
], function() {
	// everything loaded
});