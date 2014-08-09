<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;
use App;
use FormHelpers;
use ObjectHelpers;
use Validator;
use Session;
use DB;
use Exception;
use Redirect;
use Config;
use Response;
use Upload;
use Csrf;
use EloquentHelpers;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\UploadPoint;

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
		
		$mediaItems = MediaItem::with("liveStreamItem", "videoItem")->search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($mediaItems as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			$hasVod = !is_null($a->videoItem);
			$vodEnabled = $hasVod ? (boolean) $a->videoItem->enabled : null;
			$hasStream = !is_null($a->liveStreamItem);
			$streamEnabled = $hasStream ? (boolean) $a->liveStreamItem->enabled : null;
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
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"hasVod"		=> $hasVodStr,
				"hasVodCss"		=> $vodEnabled ? "text-success" : "text-danger",
				"hasStream"		=> $hasStreamStr,
				"hasStreamCss"	=> $streamEnabled ? "text-success" : "text-danger",
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/media/edit/" . $a->id,
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
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
	
		if ($formSubmitted) {
			// throws exception if token invalid
			Csrf::check();
		};
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $mediaItem, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $mediaItem, "name")),
			array("description", ObjectHelpers::getProp("", $mediaItem, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $mediaItem, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannerFile", "id")),
			array("vod-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "videoItem"))?"1":"0"),
			array("vod-enabled", ObjectHelpers::getProp(false, $mediaItem, "videoItem", "enabled")?"y":""),
			array("vod-name", ObjectHelpers::getProp("", $mediaItem, "videoItem", "name")),
			array("vod-description", ObjectHelpers::getProp("", $mediaItem, "videoItem", "description")),
			array("vod-cover-art-id", ObjectHelpers::getProp("", $mediaItem, "videoItem", "coverArtFile", "id")),
			array("vod-video-id", ObjectHelpers::getProp("", $mediaItem, "videoItem", "sourceFile", "id")),
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded_for_input")),
			array("vod-publish-time", ObjectHelpers::getProp("", $mediaItem, "videoItem", "scheduled_publish_time_for_input")),
			array("vod-live-recording", ObjectHelpers::getProp("", $mediaItem, "videoItem", "is_live_recording")),
			array("stream-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "liveStreamItem"))?"1":"0"),
			array("stream-enabled", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "enabled")?"y":""),
			array("stream-name", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "name")),
			array("stream-description", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "description")),
			array("stream-cover-art-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "coverArtFile", "id")),
			array("stream-live-time", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "scheduled_live_time")),
			array("stream-stream-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "liveStream", "id"))
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"vodVideoFile"			=> FormHelpers::getFileInfo($formData['vod-video-id']),
			"vodCoverArtFile"		=> FormHelpers::getFileInfo($formData['vod-cover-art-id']),
			"streamCoverArtFile"	=> FormHelpers::getFileInfo($formData['stream-cover-art-id'])
		);
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('valid_stream_id', FormHelpers::getValidStreamValidatorFunction());
			Validator::extend('my_date', FormHelpers::getValidDateValidatorFunction());
			
			$modelCreated = DB::transaction(function() use (&$formData, &$mediaItem, &$errors) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500'),
					'cover-image-id'	=> array('valid_file_id'),
					'side-banners-image-id'	=> array('valid_file_id'),
					'vod-name'	=> array('max:50'),
					'vod-description'	=> array('max:500'),
					'vod-video-id'	=> array('required_if:vod-added,1', 'valid_file_id'),
					'vod-cover-art-id'	=> array('valid_file_id'),
					'vod-time-recorded'	=> array('my_date'),
					'vod-publish-time'	=> array('my_date'),
					'stream-name'	=> array('max:50'),
					'stream-description'	=> array('max:500'),
					'stream-cover-art-id'	=> array('valid_file_id'),
					'stream-live-time'	=> array('my_date'),
					'stream-stream-id'	=> array('valid_stream_id')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
					'vod-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'vod-cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-video-id.required_if'	=> FormHelpers::getRequiredMsg(),
					'vod-video-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-time-recorded.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'stream-name.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'stream-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'stream-cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'stream-live-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
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
					$file = Upload::register(Config::get("uploadPoints.coverImage"), $coverImageId, $mediaItem->coverFile);
					EloquentHelpers::associateOrNull($mediaItem->coverFile(), $file);
					
					$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
					$file = Upload::register(Config::get("uploadPoints.sideBannersImage"), $sideBannerFileId, $mediaItem->sideBannerFile);
					EloquentHelpers::associateOrNull($mediaItem->sideBannerFile(), $file);
					
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
						$mediaItemVideo->time_recorded = FormHelpers::nullIfEmpty(strtotime($formData['vod-time-recorded']));
						$mediaItemVideo->name = FormHelpers::nullIfEmpty($formData['vod-name']);
						$mediaItemVideo->description = FormHelpers::nullIfEmpty($formData['vod-description']);
						$mediaItemVideo->enabled = FormHelpers::toBoolean($formData['vod-enabled']);
						$mediaItemVideo->scheduled_publish_time = FormHelpers::nullIfEmpty(strtotime($formData['vod-publish-time']));
						
						$vodVideoId = FormHelpers::nullIfEmpty($formData['vod-video-id']);
						$file = Upload::register(Config::get("uploadPoints.vodVideo"), $vodVideoId, $mediaItemVideo->sourceFile);
						EloquentHelpers::associateOrNull($mediaItemVideo->sourceFile(), $file);
						
						$vodCoverArtId = FormHelpers::nullIfEmpty($formData['vod-cover-art-id']);
						$file = Upload::register(Config::get("uploadPoints.vodCoverArt"), $vodCoverArtId, $mediaItemVideo->coverArtFile);
						EloquentHelpers::associateOrNull($mediaItemVideo->coverArtFile(), $file);
					}
					else {
						// remove video model if there is one
						if (!is_null($mediaItem->videoItem)) {
							// remove source file and cover art file (if there is one)
							Upload::delete($mediaItem->videoItem->sourceFile);
							Upload::delete($mediaItem->videoItem->coverArtFile);
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
						$mediaItemLiveStream->scheduled_live_time = FormHelpers::nullIfEmpty(strtotime($formData['stream-live-time']));
						
						$streamCoverArtId = FormHelpers::nullIfEmpty($formData['stream-cover-art-id']);
						$file = Upload::register(Config::get("uploadPoints.streamCoverArt"), $streamCoverArtId, $mediaItemLiveStream->coverArtFile);
						EloquentHelpers::associateOrNull($mediaItemLiveStream->coverArtFile(), $file);
						
						if (!is_null(FormHelpers::nullIfEmpty($formData['stream-stream-id']))) {
							$liveStream = LiveStream::find(intval($formData['stream-stream-id'], 10));
							if (is_null($liveStream)) {
								throw(new Exception("Live stream no longer exists in transaction."));
							}
							$mediaItemLiveStream->liveStream()->associate($liveStream);
						}
					}
					else {
						// remove livestream model if there is one
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
		$view->additionalForm = $additionalFormData;
		$view->formErrors = $errors;
		// used to uniquely identify these file upload points on the site. Must not be duplicated for different upload points.
		$view->coverImageUploadPointId = Config::get("uploadPoints.coverImage");
		$view->sideBannersImageUploadPointId = Config::get("uploadPoints.sideBannersImage");
		$view->vodVideoUploadPointId = Config::get("uploadPoints.vodVideo");
		$view->vodCoverArtUploadPointId = Config::get("uploadPoints.vodCoverArt");
		$view->streamCoverArtUploadPointId = Config::get("uploadPoints.streamCoverArt");
		$view->cancelUri = Config::get("custom.admin_base_url") . "/media";
	
		$this->setContent($view, "media", "media-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$mediaItem = MediaItem::find($id);
				if (!is_null($mediaItem)) {
					// mark any related files as no longer in use (so they will be removed)
					Upload::delete(array(
						$mediaItem->sideBannerFile,
						$mediaItem->coverFile,
						ObjectHelpers::getProp(null, $mediaItem->videoItem, "sourceFile"),
						ObjectHelpers::getProp(null, $mediaItem->videoItem, "coverArtFile")
					));
					
					if ($mediaItem->delete() === false) {
						throw(new Exception("Error deleting MediaItem."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
	
	// json data for ajaxSelect element
	// route to this in routes.php
	public function handleAjaxSelect() {
		$resp = array("success"=>false, "payload"=>null);
		
		if (Csrf::hasValidToken() && Auth::isLoggedIn()) {
			$searchTerm = FormHelpers::getValue("term", "");
			$mediaItems = null;
			if (!empty($searchTerm)) {
				$mediaItems = MediaItem::search($searchTerm)->orderBy("created_at", "desc")->take(20)->get();
			}
			else {
				$mediaItems = MediaItem::orderBy("created_at", "desc")->take(20)->get();
			}
			
			$results = array();
			foreach($mediaItems as $a) {
				$results[] = array("id"=>intval($a->id), "text"=>$a->name);
			}
			$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
			$resp['success'] = true;
		}
		return Response::json($resp);
	}
}
