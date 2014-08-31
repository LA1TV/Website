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
use EloquentHelpers;
use Auth;
use JsonHelpers;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\UploadPoint;
use uk\co\la1tv\website\models\LiveStreamStateDefinition;

class MediaController extends MediaBaseController {
	
	public function getIndex() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 0);
	
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
		
		$mediaItems = MediaItem::with("liveStreamItem", "liveStreamItem.liveStream", "liveStreamItem.stateDefinition", "videoItem", "videoItem.sourceFile")->search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
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
				if ($a->videoItem->getIsAccessible()) {
					$hasVodStr .= " (LIVE!)";
				}
			}
			
			$streamState = null;
			$hasStreamStr = $hasStream ? "Yes (" : "No";
			if ($hasStream) {
				$hasStreamStr .= $streamEnabled ? "Enabled" : "Disabled";
				$hasStreamStr .= ")";
				if ($a->liveStreamItem->getIsAccessible()) {
					$hasStreamStr .= " (LIVE!)";
				}
				$streamState = $a->liveStreamItem->stateDefinition->name;
			}
			else {
				$streamState = "[N/A]";
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
				"streamState"	=> $streamState,
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/media/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->editEnabled = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 1);
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/media/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/media/delete";
		$this->setContent($view, "media", "media");
	}
	
	public function anyEdit($id=null) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 1);
		
		$mediaItem = null;
		$editing = false;
		if (!is_null($id)) {
			$mediaItem = MediaItem::with("coverFile", "sideBannerFile", "videoItem", "liveStreamItem", "liveStreamItem.liveStream", "liveStreamItem.stateDefinition", "relatedItems")->find($id);
			if (is_null($mediaItem)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1

		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $mediaItem, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $mediaItem, "name")),
			array("description", ObjectHelpers::getProp("", $mediaItem, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $mediaItem, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannerFile", "id")),
			array("publish-time", ObjectHelpers::getProp("", $mediaItem, "scheduled_publish_time_for_input")),
			array("vod-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "videoItem"))?"1":"0"),
			array("vod-enabled", ObjectHelpers::getProp(false, $mediaItem, "videoItem", "enabled")?"y":""),
			array("vod-name", ObjectHelpers::getProp("", $mediaItem, "videoItem", "name")),
			array("vod-description", ObjectHelpers::getProp("", $mediaItem, "videoItem", "description")),
			array("vod-cover-art-id", ObjectHelpers::getProp("", $mediaItem, "videoItem", "coverArtFile", "id")),
			array("vod-video-id", ObjectHelpers::getProp("", $mediaItem, "videoItem", "sourceFile", "id")),
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded_for_input")),
			array("stream-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "liveStreamItem"))?"1":"0"),
			array("stream-enabled", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "enabled")?"y":""),
			array("stream-state", ObjectHelpers::getProp(LiveStreamStateDefinition::first()->id, $mediaItem, "liveStreamItem", "stateDefinition", "id")),
			array("stream-being-recorded", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "being_recorded")?"y":""),
			array("stream-name", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "name")),
			array("stream-description", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "description")),
			array("stream-info-msg", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "information_msg")),
			array("stream-cover-art-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "coverArtFile", "id")),
			array("stream-stream-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "liveStream", "id")),
			array("related-items", json_encode(array()))
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"vodVideoFile"			=> FormHelpers::getFileInfo($formData['vod-video-id']),
			"vodCoverArtFile"		=> FormHelpers::getFileInfo($formData['vod-cover-art-id']),
			"streamCoverArtFile"	=> FormHelpers::getFileInfo($formData['stream-cover-art-id']),
			"relatedItemsInput"		=> null,
			"relatedItemsInitialData"	=> null
		);
		
		
		if (!$formSubmitted) {
			$additionalFormData['relatedItemsInput'] = ObjectHelpers::getProp(json_encode(array()), $mediaItem, "related_items_for_input");
			$additionalFormData['relatedItemsInitialData'] = ObjectHelpers::getProp(json_encode(array()), $mediaItem, "related_items_for_orderable_list");
		}
		else {
			$additionalFormData['relatedItemsInput'] = MediaItem::generateInputValueForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData['related-items'], true));
			$additionalFormData['relatedItemsInitialData'] = MediaItem::generateInitialDataForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData['related-items'], true));
		}
		
		$liveStreamStateDefinitions = LiveStreamStateDefinition::orderBy("id", "asc")->get();
		$additionalFormData['streamStateButtonsData'] = array();
		foreach($liveStreamStateDefinitions as $a) {
			$additionalFormData['streamStateButtonsData'][] = array(
				"id"	=> intval($a->id),
				"text"	=> $a->name
			);
		}
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('valid_stream_id', FormHelpers::getValidStreamValidatorFunction());
			Validator::extend('my_date', FormHelpers::getValidDateValidatorFunction());
			Validator::extend('valid_related_items', function($attribute, $value, $parameters) {
				return MediaItem::isValidIdsFromAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
			});
			Validator::extend('valid_stream_state_id', function($attribute, $value, $parameters) {
				return !is_null(LiveStreamStateDefinition::find(intval($value)));
			});
			Validator::extend('not_specified', function($attribute, $value, $parameters) {
				return false;
			});
			
			$modelCreated = DB::transaction(function() use (&$formData, &$mediaItem, &$errors) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500'),
					'cover-image-id'	=> array('valid_file_id'),
					'side-banners-image-id'	=> array('valid_file_id'),
					'publish-time'	=> array('my_date'),
					'vod-name'	=> array('max:50'),
					'vod-description'	=> array('max:500'),
					'vod-video-id'	=> array('required_if:vod-added,1', 'valid_file_id'),
					'vod-cover-art-id'	=> array('valid_file_id'),
					'vod-time-recorded'	=> array('my_date'),
					'stream-state'	=> array('required', 'valid_stream_state_id'),
					'stream-name'	=> array('max:50'),
					'stream-description'	=> array('max:500'),
					'stream-info-msg'	=> array('max:500'),
					'stream-cover-art-id'	=> array('valid_file_id'),
					'stream-stream-id'	=> array('valid_stream_id'),
					'related-items'	=> array('required', 'valid_related_items')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
					'vod-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'vod-cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-video-id.required_if'	=> FormHelpers::getRequiredMsg(),
					'vod-video-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-time-recorded.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-time-recorded.not_specified'	=> "This cannot be set if this is a recording of a live stream. The time will be inferred from the scheduled live time.",
					'stream-state.required'	=> FormHelpers::getRequiredMsg(),
					'stream-state.valid_stream_state_id'	=> FormHelpers::getGenericInvalidMsg(),
					'stream-name.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'stream-description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'stream-info-msg.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'stream-cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'stream-stream-id.valid_stream_id'	=> FormHelpers::getInvalidStreamMsg(),
					'related-items.valid_related_items'	=> FormHelpers::getGenericInvalidMsg()
				));
				
				$validator->sometimes("vod-time-recorded", "not_specified", function($input) use (&$formData) {
					return $formData['stream-added'] === "1";
				});
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($mediaItem)) {
						$mediaItem = new MediaItem();
					}
					
					$mediaItem->name = $formData['name'];
					$mediaItem->description = FormHelpers::nullIfEmpty($formData['description']);
					$mediaItem->enabled = FormHelpers::toBoolean($formData['enabled']);
					$mediaItem->scheduled_publish_time = FormHelpers::nullIfEmpty(strtotime($formData['publish-time']));
					
					$coverImageId = FormHelpers::nullIfEmpty($formData['cover-image-id']);
					$file = Upload::register(Config::get("uploadPoints.coverImage"), $coverImageId, $mediaItem->coverFile);
					EloquentHelpers::associateOrNull($mediaItem->coverFile(), $file);
					
					$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
					$file = Upload::register(Config::get("uploadPoints.sideBannersImage"), $sideBannerFileId, $mediaItem->sideBannerFile);
					EloquentHelpers::associateOrNull($mediaItem->sideBannerFile(), $file);
					
					$mediaItem->relatedItems()->detach(); // detaches all
					$ids = json_decode($formData['related-items'], true);
					if (count($ids) > 0) {
						$mediaItems = MediaItem::whereIn("id", $ids)->get();
						foreach($mediaItems as $a) {
							$mediaItem->relatedItems()->attach($a, array("position"=>array_search(intval($a->id), $ids, true)));
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
						
						$mediaItemVideo->time_recorded = FormHelpers::nullIfEmpty(strtotime($formData['vod-time-recorded']));
						$mediaItemVideo->name = FormHelpers::nullIfEmpty($formData['vod-name']);
						$mediaItemVideo->description = FormHelpers::nullIfEmpty($formData['vod-description']);
						$mediaItemVideo->enabled = FormHelpers::toBoolean($formData['vod-enabled']);
						
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
						$mediaItemLiveStream->information_msg = FormHelpers::nullIfEmpty($formData['stream-info-msg']);
						$mediaItemLiveStream->being_recorded = FormHelpers::toBoolean($formData['stream-being-recorded']);
						$mediaItemLiveStream->enabled = FormHelpers::toBoolean($formData['stream-enabled']);
						$mediaItemLiveStream->stateDefinition()->associate(LiveStreamStateDefinition::find($formData['stream-state']));
						

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
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 1);
	
		$resp = array("success"=>false);
		
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$mediaItem = MediaItem::find($id);
				if (!is_null($mediaItem)) {
					if ($mediaItem->isDeletable()) {
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
					else {
						$resp['msg'] = "This media item cannot be deleted at the moment as it is being used in other places.";
					}
				}
			});
		}
		return Response::json($resp);
	}
	
	// json data for ajaxSelect element
	public function postAjaxselect() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 0);
	
		$resp = array("success"=>false, "payload"=>null);
		
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
		return Response::json($resp);
	}
}
