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
use Redirect;
use Config;
use Response;
use Upload;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\File;

class MediaController extends MediaBaseController {

	public function getIndex() {
		$view = View::make('home.admin.media.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noMediaItems = MediaItem::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noMediaItems);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noMediaItems-1) {
			App::abort(404);
			return;
		}
		
		$mediaItems = MediaItem::with("liveStreamItem", "videoItem")->search($searchTerm)->skip(FormHelpers::getPageStartIndex())->take(FormHelpers::getPageNoItems())->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($mediaItems as $a) {
			$hasVod = !is_null($a->videoItem);
			$vodEnabled = $hasVod ? (boolean) $a->videoItem->enabled : null;
			$hasStream = !is_null($a->streamItem);
			$streamEnabled = $hasStream ? (boolean) $a->streamItem->enabled : null;
			$hasVodStr = $hasVod ? "Yes (" : "No";
			if ($hasVod) {
				$hasVodStr .= $vodEnabled ? "Enabled" : "Disabled";
				$hasVodStr .= ")";
			}
			
			$hasStreamStr = $hasStream ? "Yes (" : "No";
			if ($hasStream) {
				$hasStreamStr .= $streamEnabled ? "Enabled" : "Disabled";
				$hasStreamStr .= ")";
			}
			
			$tableData[] = array(
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"has_vod"		=> $hasVodStr,
				"has_vod_css"	=> $vodEnabled === TRUE ? "text-success" : "text-danger",
				"has_stream"	=> $hasStreamStr,
				"has_stream_css"	=> $streamEnabled === TRUE ? "text-success" : "text-danger",
				"time_created"	=> $a->created_at->toDateTimeString(),
				"edit_uri"		=> Config::get("custom.admin_base_url") . "/media/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/media/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/media/delete";
		$this->setContent($view, "media", "media");
	}
	
	public function anyEdit($id=null) {
		
		$mediaItem = null;
		$editing = false;
		if (!is_null($id)) {
			$mediaItem = MediaItem::with("coverFile", "sideBannerFile", "videoItem", "liveStreamItem", "liveStreamItem.liveStream")->find($id);
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
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannerFile", "id")),
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
			array("stream-stream-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "liveStream", "id"))
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
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('valid_stream_id', FormHelpers::getValidStreamValidatorFunction());
			
			// TODO: date validation isn't good enough. need to check there is a time not just date
			
			$modelCreated = DB::transaction(function() use (&$formData, &$mediaItem, &$errors) {
			
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
					'stream-stream-id'	=> array('valid_stream_id')
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
							$file = $mediaItem->coverFile;
							$file->in_use = false;
							if ($file->save() === false) {
								throw(new Exception("Error deleting MediaItem cover file."));
							}
							$mediaItem[$mediaItem->coverFile->getForeignKey()] = null;
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
							$file = $mediaItem->sideBannerFile;
							$file->in_use = false;
							if ($file->save() === false) {
								throw(new Exception("Error deleting MediaItem side banner file."));
							}
							$mediaItem[$mediaItem->sideBannerFile->getForeignKey()] = null;
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
					$errors = $validator->messages();
					return false;
				}
			});
			
			if ($modelCreated) {
				return Redirect::to(Config::get("custom.admin_base_url") . "/media");
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
		$view->formErrors = $errors;
		// used to uniquely identify these file upload points on the site. Must not appear anywhere else.
		$view->coverImageUploadPointId = 2;
		$view->sideBannersImageUploadPointId = 1;
		$view->vodVideoUploadPointId = 3;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/media";
	
		$this->setContent($view, "media", "media-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$mediaItem = MediaItem::find($id);
				if (!is_null($mediaItem)) {
					// mark any related files as no longer in use (so they will be removed)
					$files = array(
						$mediaItem->sideBannerFile,
						$mediaItem->coverFile
					);
					foreach($files as $a) {
						if (!is_null($a)) {
							$a->markReadyForDelete();
							$a->save();
						}
					}
					if ($mediaItem->delete()) {
						$resp['success'] = true;
					}
				}
			});
		}
		return Response::json($resp);
	}
}
