<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use App;
use FormHelpers;
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
			$mediaItem = MediaItem::find($id);
			if (is_null($mediaItem)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", false),
			array("name", ""),
			array("description", ""),
			array("cover-image-id", ""),
			array("cover-image-file-name", ""),
			array("cover-image-file-size", ""),
			array("side-banners-image-id", ""),
			array("side-banners-image-file-name", ""),
			array("side-banners-image-file-size", ""),
			array("vod-enabled", false),
			array("vod-name", ""),
			array("vod-description", ""),
			array("vod-video-id", ""),
			array("vod-video-file-name", ""),
			array("vod-video-file-size", ""),
			array("vod-publish-time", ""),
			array("vod-live-recording", false),
			array("stream-enabled", false),
			array("stream-name", ""),
			array("stream-description", ""),
			array("stream-live-time", ""),
			array("stream-stream-id", "")
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
