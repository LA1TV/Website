<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use App;
use FormHelpers;
use ObjectHelpers;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\LiveStream;

class MediaController extends MediaBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.media.index'), "media", "media");
	}
	
	public function getEdit($id=null) {
		
		$mediaItem = null;
		$editing = false;
		if (!is_null($id)) {
			$mediaItem = MediaItem::with("coverFile", "sideBannersFile", "videoItem", "liveStreamItem", "liveStreamItem.liveStream")->find($id);
			if (is_null($mediaItem)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		// populate $formData with default values or received values
		
		
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $mediaItem, "enabled")),
			array("name", ObjectHelpers::getProp("", $mediaItem, "name")),
			array("description", ObjectHelpers::getProp("", $mediaItem, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $mediaItem, "coverFile", "id")),
			array("cover-image-file-name", ObjectHelpers::getProp("", $mediaItem, "coverFile", "filename")),
			array("cover-image-file-size", ObjectHelpers::getProp("", $mediaItem, "coverFile", "size")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannersFile", "id")),
			array("side-banners-image-file-name", ObjectHelpers::getProp("", $mediaItem, "sideBannersFile", "filename")),
			array("side-banners-image-file-size",  ObjectHelpers::getProp("", $mediaItem, "sideBannersFile", "size")),
			array("vod-enabled", ObjectHelpers::getProp(false, $mediaItem, "videoItem", "enabled")),
			array("vod-name", ObjectHelpers::getProp("", $mediaItem, "videoItem", "name")),
			array("vod-description", ObjectHelpers::getProp("", $mediaItem, "videoItem", "description")),
			array("vod-video-id", ""), // TODO
			array("vod-video-file-name", ""), //TODO
			array("vod-video-file-size", ""), //TODO
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded")),
			array("vod-publish-time", ObjectHelpers::getProp("", $mediaItem, "videoItem", "scheduled_publish_time")),
			array("vod-live-recording", ObjectHelpers::getProp(false, $mediaItem, "videoItem", "is_live_recording")),
			array("stream-enabled", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "enabled")),
			array("stream-name", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "name")),
			array("stream-description", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "description")),
			array("stream-live-time", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "scheduled_live_time")),
			array("stream-stream-id", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "liveStream", "id"))
		), !$editing);
		
		if (!is_null($mediaItem)) {
			// validate input. If it's all valid create new model
			
			
			// if not valid then return form again with errors
		}
		
		$liveStreams = LiveStream::orderBy("name", "asc")->orderBy("description", "asc")->get();
		$streamOptions = array();
		$streamOptions[] = array("id"=>"", "name"=>"[None]");
		
		foreach($liveStreams as $a) {
			$name = $a->name;
			if (!$a->enabled) {
				$name .= " [Disabled]";
			}
			$streamOptions[] = array("id"=>$a->id, "name"=>$name);
		}
		
		$view = View::make('home.admin.media.edit');
		$view->editing = $editing;
		$view->streamOptions = $streamOptions;
		$view->form = $formData;
	
		$this->setContent($view, "media", "media-edit");
	}
}
