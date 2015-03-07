define([
	"videojs",
	"./page-data",
	"lib/videojs-markers"
], function(videojs, PageData) {

	// register the swf for flash playback
	videojs.options.flash.swf = PageData.get("baseUrl")+"/assets/video-js/flash/mangui-video-js.swf";
	
	// modify the flash tech so that the correct sources get passed to the modified flash player 
	// type should be all lowercase
	videojs.Flash.formats["application/x-mpegurl"] = "MP4";
});