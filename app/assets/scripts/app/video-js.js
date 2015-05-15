define([
	"videojs",
	"./page-data",
	"lib/videojs-markers",
	"lib/videojs-thumbnails"
], function(videojs, PageData) {

	// register the swf for flash playback
	videojs.options.flash.swf = PageData.get("baseUrl")+"/assets/video-js/flash/video-js.swf";
});