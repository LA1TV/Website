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
		// VIDEOJS HLS STUFF
		// can't be bothered trying to figure out which files have dependencies on which,
		// so this order is the order they are included on the demo page
		// and I have given each one the above file as a dependency, meaning they should
		// always be loaded in the same order that works.
		// https://github.com/videojs/videojs-contrib-hls/blob/931130836c566704ae5c8d69fd8594410db7f5f2/example.html
		"lib/videojs-media-sources": ["lib/video"],
		"lib/videojs-contrib-hls/videojs-hls": ["lib/video", "lib/videojs-media-sources"],
		"lib/videojs-contrib-hls/xhr": ["lib/videojs-contrib-hls/videojs-hls"],
		"lib/videojs-contrib-hls/flv-tag": ["lib/videojs-contrib-hls/xhr"],
		"lib/videojs-contrib-hls/exp-golomb": ["lib/videojs-contrib-hls/flv-tag"],
		"lib/videojs-contrib-hls/h264-stream": ["lib/videojs-contrib-hls/exp-golomb"],
		"lib/videojs-contrib-hls/aac-stream": ["lib/videojs-contrib-hls/h264-stream"],
		"lib/videojs-contrib-hls/segment-parser": ["lib/videojs-contrib-hls/aac-stream"],
		"lib/videojs-contrib-hls/stream": ["lib/videojs-contrib-hls/segment-parser"],
		"lib/videojs-contrib-hls/m3u8/m3u8-parser": ["lib/videojs-contrib-hls/stream"],
		"lib/videojs-contrib-hls/playlist-loader": ["lib/videojs-contrib-hls/m3u8/m3u8-parser"],
		"lib/videojs-contrib-hls/decrypter": ["lib/videojs-contrib-hls/playlist-loader"],
		"lib/videojs-contrib-hls/bin-utils": ["lib/videojs-contrib-hls/decrypter"]
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