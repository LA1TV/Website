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
		},
		"lib/videojs-media-sources": ["lib/video"],
		"lib/videojs-contrib-hls/videojs-hls": ["lib/video"],
		"lib/videojs-contrib-hls/aac-stream": ["lib/videojs-contrib-hls/videojs-hls", "lib/videojs-contrib-hls/flv-tag"],
		"lib/videojs-contrib-hls/bin-utils": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/decrypter": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/exp-golomb": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/flv-tag": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/h264-stream": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/playlist-loader": ["lib/videojs-contrib-hls/videojs-hls", "lib/videojs-contrib-hls/stream"],
		"lib/videojs-contrib-hls/segment-parser": ["lib/videojs-contrib-hls/videojs-hls", "lib/videojs-contrib-hls/flv-tag"],
		"lib/videojs-contrib-hls/stream": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/xhr": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/m3u8/m3u8-parser": ["lib/videojs-contrib-hls/videojs-hls"]
	}
});

(function() {
	var startTime = new Date().getTime();

	require([
		"app/logger",
		"app/google-analytics",
		"app/error-handler",
		"lib/bootstrap",
		"app/fit-text-handler",
		"app/synchronised-time",
		"app/video-js",
		"app/pages/embed/player-page",
	], function(logger, googleAnalytics) {
		// everything loaded
		logger.info("Embeddable player loaded.");
		googleAnalytics.registerModulesLoadTime("Embed", new Date().getTime() - startTime);
	});

})();