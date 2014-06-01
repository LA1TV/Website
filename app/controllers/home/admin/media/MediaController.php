<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use App;
use FormHelpers;
use ObjectHelpers;
use AllowedFileTypesHelper;
use Validator;
use Session;
use DB;
use Exception;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemLiveStream;
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
			array("vod-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "videoItem")) ? "1":"0"),
			array("vod-enabled", ObjectHelpers::getProp("", $mediaItem, "videoItem", "enabled")),
			array("vod-name", ObjectHelpers::getProp("", $mediaItem, "videoItem", "name")),
			array("vod-description", ObjectHelpers::getProp("", $mediaItem, "videoItem", "description")),
			array("vod-video-id", ""), // TODO
			array("vod-video-file-name", ""), //TODO
			array("vod-video-file-size", ""), //TODO
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded")),
			array("vod-publish-time", ObjectHelpers::getProp("", $mediaItem, "videoItem", "scheduled_publish_time")),
			array("vod-live-recording", ObjectHelpers::getProp("", $mediaItem, "videoItem", "is_live_recording")),
			array("stream-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "liveStreamItem")) ? "1":"0"),
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
			
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('valid_stream_id', FormHelpers::getValidStreamValidatorFunction());
			
			// TODO: date validation isn't good enough. need to check there is a time not just date
			
			$modelCreated = DB::transaction(function() use (&$formData, &$mediaItem) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500'),
					'cover-image-id'	=> array('valid_file_id:'.implode("-", AllowedFileTypesHelper::getImages())),
					'side-banners-image-id'	=> array('valid_file_id:'.implode("-", AllowedFileTypesHelper::getImages())),
					'vod-name'	=> array('max:50'),
					'vod-description'	=> array('max:500'),
			//		'vod-video-id'	=> array(('required_if:vod-added,1', 'valid_file_id:'.implode("-", AllowedFileTypesHelper::getVideos())), //TODO
					'vod-time-recorded'	=> array('date'),
					'vod-publish-time'	=> array('date'),
					'stream-name'	=> array('max:50'),
					'stream-description'	=> array('max:500'),
					'stream-live-time'	=> array('date'),
					'stream-stream-id'	=> array(('valid_stream_id'))
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
					'vod-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
			//		'vod-video-id.required_if'	=> FormHelpers::getRequiredMsg(), // TODO
			//		'vod-video-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(), //TODO
					'vod-time-recorded.date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-publish-time.date'	=> FormHelpers::getInvalidTimeMsg(),
					'stream-name.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'stream-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'stream-live-time.date'	=> FormHelpers::getInvalidTimeMsg(),
					'stream-stream-id.valid_stream_id'	=> FormHelpers::getInvalidStreamMsg()
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($mediaItem)) {
						$mediaItem = new MediaItem();
					}
					
					$mediaItem->name = $formData['name'];
					$mediaItem->description = FormHelpers::nullIfEmpty($formData['description']);
					$mediaItem->enabled = FormHelpers::toBoolean($formData['enabled']);
					$coverImageId = FormHelpers::nullIfEmpty($formData['cover-image-id']);
					if (!is_null($coverImageId)) {
						$coverImageId = intval($coverImageId, 10);
						// we know this file will still exist because all of this is in transaction and file existed during validation
						$file = File::find($coverImageId);
						if (is_null($file)) {
							throw(new Exception("File no longer exists in transaction."));
						}
						$file->in_use = true; // mark file as being in_use now
						if ($file->save() === false) {
							throw(new Exception("Error saving file model."));
						}
						$mediaItem->coverFile()->associate($file);
					}
					else {
						// remove coverimage if there currently is one
						if (!is_null($mediaItem->coverFile)) {
							if ($mediaItem->coverFile->delete() === false) {
								throw(new Exception("Error deleting MediaItem cover file."));
							}
						}
					}
					$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
					if (!is_null($sideBannerFileId)) {
						$sideBannerFileId = intval($sideBannerFileId, 10);
						$file = File::find($sideBannerFileId);
						if (is_null($file)) {
							throw(new Exception("File no longer exists in transaction."));
						}
						$file->in_use = true; // mark file as being in_use now
						if ($file->save() === false) {
							throw(new Exception("Error saving file model."));
						}
						$mediaItem->sideBannerFile()->associate($file);
					}
					else {
						// remove banner if there already is one
						if (!is_null($mediaItem->sideBannerFile)) {
							if ($mediaItem->sideBannerFile->delete() === false) {
								throw(new Exception("Error deleting MediaItem side banner file."));
							}
						}
					}
					
					// vod
					$mediaItemVideo = null;
					if ($formData['vod-added'] === "1") {
					
						// create MediaItemVideo if doesn't exist, otherwise retrieve it
						if (!is_null($mediaItem->videoItem)) {
							$mediaItemVideo = $mediaItem->videoItem;
						}
						else {
							$mediaItemVideo = new MediaItemVideo();
						}
						
						$mediaItemVideo->is_live_recording = FormHelpers::toBoolean($formData['vod-live-recording']);
						$mediaItemVideo->time_recorded = FormHelpers::nullIfEmpty($formData['vod-time-recorded']);
						$mediaItemVideo->name = FormHelpers::nullIfEmpty($formData['vod-name']);
						$mediaItemVideo->description = FormHelpers::nullIfEmpty($formData['vod-description']);
						$mediaItemVideo->enabled = FormHelpers::toBoolean($formData['vod-enabled']);
						$mediaItemVideo->scheduled_publish_time = FormHelpers::nullIfEmpty($formData['vod-publish-time']);
					}
					else {
						// remove video model if there is one
						if (!is_null($mediaItem->videoItem)) {
							if ($mediaItem->videoItem->delete() === false) {
								throw(new Exception("Error deleting MediaItemVideo."));
							}
						}
					}
					
					
					// stream
					$mediaItemLiveStream = null;
					if ($formData['stream-added'] === "1") {
					
						// create MediaItemLiveStream if doesn't exist, otherwise retrieve it
						if (!is_null($mediaItem->liveStreamItem)) {
							$mediaItemLiveStream = $mediaItem->liveStreamItem;
						}
						else {
							$mediaItemLiveStream = new MediaItemLiveStream();
						}
						
						$mediaItemLiveStream->name = FormHelpers::nullIfEmpty($formData['stream-name']);
						$mediaItemLiveStream->description = FormHelpers::nullIfEmpty($formData['stream-description']);
						$mediaItemLiveStream->enabled = FormHelpers::toBoolean($formData['stream-enabled']);
						$mediaItemLiveStream->scheduled_live_time = FormHelpers::toBoolean(FormHelpers::nullIfEmpty($formData['stream-live-time']));
							
						if (!is_null(FormHelpers::nullIfEmpty($formData['stream-stream-id']))) {
							$liveStream = LiveStream::find(intval($formData['stream-stream-id'], 10));
							if (is_null($liveStream)) {
								throw(new Exception("Live stream no longer exists in transaction."));
							}
							$mediaItemLiveStream->liveStream()->associate($liveStream);
						}
					}
					else {
						// remove video model if there is one
						if (!is_null($mediaItem->liveStreamItem)) {
							if ($mediaItem->liveStreamItem->delete() === false) {
								throw(new Exception("Error deleting MediaItemLiveStream."));
							}
						}
					}
					
					if ($mediaItem->save() === false) {
						throw(new Exception("Error saving MediaItem."));
					}
					if (!is_null($mediaItemVideo)) {
						if ($mediaItem->videoItem()->save($mediaItemVideo) === false) {
							throw(new Exception("Error creating MediaItemVideo."));
						}
					}
					if (!is_null($mediaItemLiveStream)) {
						if ($mediaItem->liveStreamItem()->save($mediaItemLiveStream) === false) {
							throw(new Exception("Error creating MediaItemLiveStream."));
						}
					}
					
					// the transaction callback result is returned out of the transaction function
					return true;
				}
				else {
					return false;
				}
			});
			
			if ($modelCreated) {
				// TODO
				return "Success!";
			}
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
