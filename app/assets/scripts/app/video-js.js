define([
	"lib/video",
	"./page-data",
	"lib/pkcs7.unpad"
], function(videojs, PageData, unpad) {

	// register the swf for flash playback
	videojs.options.flash.swf = PageData.get("baseUrl")+"/assets/video-js/flash/video-js.swf";
	
	// add hls support
	// decrypter.js requires the pkcs7.unpad to be accessible at window.pkcs7.unpad
	window.pkcs7 = {unpad: unpad};
	require([
		"lib/videojs-media-sources",
		"lib/videojs-contrib-hls/videojs-hls",
		"lib/videojs-contrib-hls/aac-stream",
		"lib/videojs-contrib-hls/bin-utils",
		"lib/videojs-contrib-hls/decrypter",
		"lib/videojs-contrib-hls/exp-golomb",
		"lib/videojs-contrib-hls/flv-tag",
		"lib/videojs-contrib-hls/h264-stream",
		"lib/videojs-contrib-hls/playlist-loader",
		"lib/videojs-contrib-hls/segment-parser",
		"lib/videojs-contrib-hls/stream",
		"lib/videojs-contrib-hls/xhr",
		"lib/videojs-contrib-hls/m3u8/m3u8-parser"
	]);
});