<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use App;
use FormHelpers;
use ObjectHelpers;
use AllowedFileTypesHelper;
use Validator;
use Session;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\File;

class MediaController extends MediaBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.media.index'), "media", "media");
	}
	
	public function anyEdit($id=null) {
		
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
		
		$formSubmitted = isset($_POST['form-submitted']);
	
		
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp("", $mediaItem, "enabled")),
			array("name", ObjectHelpers::getProp("", $mediaItem, "name")),
			array("description", ObjectHelpers::getProp("", $mediaItem, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $mediaItem, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannersFile", "id")),
			array("vod-enabled", ObjectHelpers::getProp("", $mediaItem, "videoItem", "enabled")),
			array("vod-name", ObjectHelpers::getProp("", $mediaItem, "videoItem", "name")),
			array("vod-description", ObjectHelpers::getProp("", $mediaItem, "videoItem", "description")),
			array("vod-video-id", ""), // TODO
			array("vod-video-file-name", ""), //TODO
			array("vod-video-file-size", ""), //TODO
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded")),
			array("vod-publish-time", ObjectHelpers::getProp("", $mediaItem, "videoItem", "scheduled_publish_time")),
			array("vod-live-recording", ObjectHelpers::getProp("", $mediaItem, "videoItem", "is_live_recording")),
			array("stream-enabled", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "enabled")),
			array("stream-name", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "name")),
			array("stream-description", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "description")),
			array("stream-live-time", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "scheduled_live_time")),
			array("stream-stream-id", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "liveStream", "id"))
		), !$formSubmitted);
		
		// now set filenames and sizes
		$formData['cover-image-file-name'] = "";
		$formData['cover-image-file-size'] = "";
		$formData['side-banners-image-file-name'] = "";
		$formData['side-banners-image-file-size'] = "";
		if ($formData['cover-image-id'] !== "") {
			$file = File::find($formData['cover-image-id']);
			if (!is_null($file)) {
				$formData['cover-image-file-name'] = $file->filename;
				$formData['cover-image-file-size'] = $file->size;
			}
		}
		if ($formData['side-banners-image-id'] !== "") {
			$file = File::find($formData['side-banners-image-id']);
			if (!is_null($file)) {
				$formData['side-banners-image-file-name'] = $file->filename;
				$formData['side-banners-image-file-size'] = $file->size;
			}
		}
		
		if ($formSubmitted) {
			// validate input
			
			Validator::extend('valid_file_id', function($attribute, $value, $parameters) {
				if ($value === "") {
					return true;
				}
				$value = intval($value, 10);
				$file = File::find($value);
				return !(is_null($file) || $file->in_use || is_null($file->session_id) || $file->session_id !== Session::getId() || !in_array($file->getExtension(), explode("-", $parameters[0]), true));
			});
			
			// TODO: date validation isn't good enough. need to check there is a time not just date
			
			$validator = Validator::make($formData,	array(
				'name'		=> array('required', 'max:50'),
				'description'	=> array('max:500'),
				'cover-image-id'	=> array('valid_file_id:'.implode("-", AllowedFileTypesHelper::getImages())),
				'side-banners-image-id'	=> array('valid_file_id:'.implode("-", AllowedFileTypesHelper::getImages())),
				'vod-name'	=> array('required_if:vod-enabled,y', 'max:50'),
				'vod-description'	=> array('max:500'),
		//		'vod-video-id'	=> array('valid_file_id:'.implode("-", AllowedFileTypesHelper::getVideos())), //TODO
				'vod-time-recorded'	=> array('date'),
				'vod-publish-time'	=> array('date'),
				'stream-name'	=> array('required_if:stream-enabled,y', 'max:50'),
				'stream-description'	=> array('max:500'),
				'stream-live-time'	=> array('date'),
			), array(
				'name.required'		=> FormHelpers::getRequiredMsg(),
				'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
				'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
				'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
				'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
				'vod-name.required_if'	=> FormHelpers::getRequiredMsg(),
				'vod-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
				'vod-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
		//		'vod-video-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(), //TODO
				'vod-time-recorded.date'	=> FormHelpers::getInvalidTimeMsg(),
				'vod-publish-time.date'	=> FormHelpers::getInvalidTimeMsg(),
				'stream-name.required_if'	=> FormHelpers::getRequiredMsg(),
				'stream-name.max'	=> FormHelpers::getLessThanCharactersMsg(50),
				'stream-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
				'stream-live-team.date'	=> FormHelpers::getInvalidTimeMsg()
			));
			
			
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
