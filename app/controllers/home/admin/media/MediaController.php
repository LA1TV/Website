<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use uk\co\la1tv\website\models\LiveStream;

class MediaController extends MediaBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.media.index'), "media", "media");
	}
	
	public function getEdit($id=null) {
		
		$formData = array(
			"enabled"		=> true,
			"name"			=> "test",
			"description"	=> "test",
			"cover-image-id"=> "",
			"cover-image-file-name"=> "",
			"cover-image-file-size"=> "",
			"side-banners-image-id"=> "",
			"side-banners-image-file-name"=> "",
			"side-banners-image-file-size"=> "",
			"side-banners-image-file-size"=> "",
			"vod-enabled"	=> false,
			"vod-name"		=> "",
			"vod-description"=> "",
			"vod-video-id"	=> "",
			"vod-video-file-name"=> "",
			"vod-video-file-size"=> "",
			"vod-publish-time"=> "",
			"vod-live-recording"=> false,
			"stream-enabled"=> false,
			"stream-name"	=> "",
			"stream-description"=> "",
			"stream-live-time"=> "",
			"stream-stream-id"=> ""
		);
		
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
		$view->editing = false;
		$view->streamOptions = $streamOptions;
		$view->form = $formData;
	
		$this->setContent($view, "media", "media-edit");
	}
}
