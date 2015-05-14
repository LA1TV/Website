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
use Carbon;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemVideoChapter;
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
		
		$mediaItems = MediaItem::with("playlists", "liveStreamItem", "liveStreamItem.liveStream", "liveStreamItem.stateDefinition", "videoItem", "videoItem.sourceFile")->search($searchTerm)->usePagination()->orderBy("created_at", "desc")->orderBy("name", "asc")->orderBy("description", "asc")->sharedLock()->get();
		
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
				if ($a->videoItem->getIsLive()) {
					$hasVodStr .= " (LIVE!)";
				}
			}
			
			$streamState = null;
			$hasStreamStr = $hasStream ? "Yes (" : "No";
			if ($hasStream) {
				$hasStreamStr .= $streamEnabled ? "Enabled" : "Disabled";
				$hasStreamStr .= ")";
				if ($a->liveStreamItem->getIsAccessible() && intval($a->liveStreamItem->getResolvedStateDefinition()->id) === 2) {
					$hasStreamStr .= " (LIVE!)";
				}
				$streamState = $a->liveStreamItem->stateDefinition->name;
			}
			else {
				$streamState = "[N/A]";
			}
			
			$playlists = $a->playlists;
			$names = array();
			foreach($playlists as $playlist) {
				$names[] = $playlist->generateName();
			}
			$playlistsStr = count($names) > 0 ? '"'.implode('", "', $names).'"' : "[Not In A Playlist]";
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"playlists"		=> $playlistsStr,
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
			array("enabled", ObjectHelpers::getProp(true, $mediaItem, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $mediaItem, "name")),
			array("description", ObjectHelpers::getProp("", $mediaItem, "description")),
			array("email-notifications-enabled", ObjectHelpers::getProp(true, $mediaItem, "email_notifications_enabled")?"y":""),
			array("likes-enabled", ObjectHelpers::getProp(true, $mediaItem, "likes_enabled")?"y":""),
			array("comments-enabled", ObjectHelpers::getProp(true, $mediaItem, "comments_enabled")?"y":""),
			array("cover-image-id", ObjectHelpers::getProp("", $mediaItem, "coverFile", "id")),
			array("cover-art-id", ObjectHelpers::getProp("", $mediaItem, "coverArtFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannerFile", "id")),
			array("side-banners-fill-image-id", ObjectHelpers::getProp("", $mediaItem, "sideBannerFillFile", "id")),
			array("publish-time", ObjectHelpers::getProp("", $mediaItem, "scheduled_publish_time_for_input")),
			array("vod-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "videoItem"))?"1":"0"),
			array("vod-enabled", ObjectHelpers::getProp(true, $mediaItem, "videoItem", "enabled")?"y":""),
			array("vod-video-id", ObjectHelpers::getProp("", $mediaItem, "videoItem", "sourceFile", "id")),
			array("vod-time-recorded",  ObjectHelpers::getProp("", $mediaItem, "videoItem", "time_recorded_for_input")),
			array("vod-chapters", json_encode(array())),
			array("stream-added", !is_null(ObjectHelpers::getProp(null, $mediaItem, "liveStreamItem"))?"1":"0"),
			array("stream-enabled", ObjectHelpers::getProp(true, $mediaItem, "liveStreamItem", "enabled")?"y":""),
			array("stream-state", ObjectHelpers::getProp(LiveStreamStateDefinition::first()->id, $mediaItem, "liveStreamItem", "stateDefinition", "id")),
			array("stream-being-recorded", ObjectHelpers::getProp(false, $mediaItem, "liveStreamItem", "being_recorded")?"y":""),
			array("stream-info-msg", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "information_msg")),
			array("stream-stream-id", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "liveStream", "id")),
			array("stream-external-stream-url", ObjectHelpers::getProp("", $mediaItem, "liveStreamItem", "external_stream_url")),
			array("related-items", json_encode(array()))
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"sideBannersFillImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-fill-image-id']),
			"coverArtFile"			=> FormHelpers::getFileInfo($formData['cover-art-id']),
			"vodVideoFile"			=> FormHelpers::getFileInfo($formData['vod-video-id']),
			"vodChaptersInput"		=> null,
			"vodChaptersInitialData"	=> null,
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
		
		if (!$formSubmitted && !is_null($mediaItem) && !is_null($mediaItem->videoItem)) {
			$additionalFormData['vodChaptersInput'] = ObjectHelpers::getProp(json_encode(array()), $mediaItem, "videoItem", "chapters_for_input");
			$additionalFormData['vodChaptersInitialData'] = ObjectHelpers::getProp(json_encode(array()), $mediaItem, "videoItem", "chapters_for_orderable_list");
		}
		else {
			$additionalFormData['vodChaptersInput'] = MediaItemVideo::generateInputValueForChaptersOrderableList(JsonHelpers::jsonDecodeOrNull($formData['vod-chapters'], true));
			$additionalFormData['vodChaptersInitialData'] = MediaItemVideo::generateInitialDataForChaptersOrderableList(JsonHelpers::jsonDecodeOrNull($formData['vod-chapters'], true));
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
			Validator::extend('valid_vod_chapters', function($attribute, $value, $parameters) {
				return MediaItemVideo::isValidDataFromChaptersOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
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
					'side-banners-fill-image-id'	=> array('valid_file_id'),
					'cover-art-id'		=> array('valid_file_id'),
					'publish-time'	=> array('my_date'),
					'vod-video-id'	=> array('required_if:vod-added,1', 'valid_file_id'),
					'vod-time-recorded'	=> array('my_date'),
					'vod-chapters'	=> array('required', 'valid_vod_chapters'),
					'stream-state'	=> array('required', 'valid_stream_state_id'),
					'stream-info-msg'	=> array('max:500'),
					'stream-stream-id'	=> array('valid_stream_id'),
					'stream-external-stream-url'=> array('url'),
					'related-items'	=> array('required', 'valid_related_items')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-fill-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-video-id.required_if'	=> FormHelpers::getRequiredMsg(),
					'vod-video-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'vod-time-recorded.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'vod-time-recorded.not_specified'	=> "This cannot be set if this is a recording of a live stream. The time will be inferred from the scheduled live time.",
					'vod-chapters.required'	=> FormHelpers::getGenericInvalidMsg(),
					'vod-chapters.valid_vod_chapters'	=> FormHelpers::getGenericInvalidMsg(),
					'stream-state.required'	=> FormHelpers::getRequiredMsg(),
					'stream-state.valid_stream_state_id'	=> FormHelpers::getGenericInvalidMsg(),
					'stream-info-msg.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'stream-stream-id.valid_stream_id'	=> FormHelpers::getInvalidStreamMsg(),
					'stream-external-stream-url.url'	=> "This is not a valid url.",
					'related-items.required'	=> FormHelpers::getGenericInvalidMsg(),
					'related-items.valid_related_items'	=> FormHelpers::getGenericInvalidMsg()
				));
				
				$validator->sometimes("vod-time-recorded", "not_specified", function($input) use (&$formData) {
					return $formData['stream-added'] === "1" && $formData['vod-added'] === "1";
				});
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($mediaItem)) {
						$mediaItem = new MediaItem();
					}
					
					$mediaItem->name = $formData['name'];
					$mediaItem->description = FormHelpers::nullIfEmpty($formData['description']);
					$mediaItem->enabled = FormHelpers::toBoolean($formData['enabled']);
					// if the scheduled publish time is empty and this item is enabled, set it to the current time.
					// an enabled media item should always have a published time.
					$scheduledPublishTime = FormHelpers::nullIfEmpty(strtotime($formData['publish-time']));
					$mediaItem->scheduled_publish_time = !is_null($scheduledPublishTime) ? $scheduledPublishTime : Carbon::now();
					$mediaItem->email_notifications_enabled = FormHelpers::toBoolean($formData['email-notifications-enabled']);
					$mediaItem->likes_enabled = FormHelpers::toBoolean($formData['likes-enabled']);
					// if comments are being disabled then remove any existing comments.
					$commentsEnabled = FormHelpers::toBoolean($formData['comments-enabled']);
					$currentCommentsEnabled = (boolean) $mediaItem->comments_enabled;
					$mediaItem->comments_enabled = $commentsEnabled;
					if ($currentCommentsEnabled !== $commentsEnabled) {
						// remove when changing from enabled to disabled and vice versa
						// I think it might be possible for someone to make a comment during this transaction when going from enabled to disable.
						// deleting when going from disabled to enabled should always catch any that this happens to
						$mediaItem->comments()->delete();
					}
					
					$coverImageId = FormHelpers::nullIfEmpty($formData['cover-image-id']);
					$file = Upload::register(Config::get("uploadPoints.coverImage"), $coverImageId, $mediaItem->coverFile);
					EloquentHelpers::associateOrNull($mediaItem->coverFile(), $file);
					
					$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
					$file = Upload::register(Config::get("uploadPoints.sideBannersImage"), $sideBannerFileId, $mediaItem->sideBannerFile);
					EloquentHelpers::associateOrNull($mediaItem->sideBannerFile(), $file);
					
					$sideBannerFillFileId = FormHelpers::nullIfEmpty($formData['side-banners-fill-image-id']);
					$file = Upload::register(Config::get("uploadPoints.sideBannersFillImage"), $sideBannerFillFileId, $mediaItem->sideBannerFillFile);
					EloquentHelpers::associateOrNull($mediaItem->sideBannerFillFile(), $file);
					
					$coverArtId = FormHelpers::nullIfEmpty($formData['cover-art-id']);
					$file = Upload::register(Config::get("uploadPoints.coverArt"), $coverArtId, $mediaItem->coverArtFile);
					EloquentHelpers::associateOrNull($mediaItem->coverArtFile(), $file);
					
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
						$mediaItemVideo->enabled = FormHelpers::toBoolean($formData['vod-enabled']);
						
						$vodVideoId = FormHelpers::nullIfEmpty($formData['vod-video-id']);
						$file = Upload::register(Config::get("uploadPoints.vodVideo"), $vodVideoId, $mediaItemVideo->sourceFile);
						EloquentHelpers::associateOrNull($mediaItemVideo->sourceFile(), $file);
						
						if ($mediaItemVideo->chapters()->count() > 0) {
							if (!$mediaItemVideo->chapters()->delete()) { // remove all chapters
								throw(new Exception("Error deleting MediaItemVideo chapters."));
							}
						}
						// now add the chapters again
						$chapterData = json_decode($formData['vod-chapters'], true);
						foreach($chapterData as $chapter) {
							$chapterModel = new MediaItemVideoChapter(array(
								"title"	=> trim($chapter['title']),
								"time"	=> $chapter['time']
							));
							$mediaItemVideo->chapters()->save($chapterModel);
						}
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
						
						$mediaItemLiveStream->information_msg = FormHelpers::nullIfEmpty($formData['stream-info-msg']);
						$mediaItemLiveStream->being_recorded = FormHelpers::toBoolean($formData['stream-being-recorded']);
						$mediaItemLiveStream->enabled = FormHelpers::toBoolean($formData['stream-enabled']);
						$mediaItemLiveStream->stateDefinition()->associate(LiveStreamStateDefinition::find($formData['stream-state']));
						
						if (!is_null(FormHelpers::nullIfEmpty($formData['stream-stream-id']))) {
							$liveStream = LiveStream::find(intval($formData['stream-stream-id'], 10));
							if (is_null($liveStream)) {
								throw(new Exception("Live stream no longer exists in transaction."));
							}
							$mediaItemLiveStream->liveStream()->associate($liveStream);
						}
						else {
							EloquentHelpers::setForeignKeyNull($mediaItemLiveStream->liveStream());
						}
						
						$mediaItemLiveStream->external_stream_url = FormHelpers::nullIfEmpty($formData['stream-external-stream-url']);
						
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
					
					$mediaItem->relatedItems()->detach(); // detaches all
					$ids = json_decode($formData['related-items'], true);
					if (count($ids) > 0) {
						$mediaItems = MediaItem::whereIn("id", $ids)->get();
						foreach($mediaItems as $a) {
							$mediaItem->relatedItems()->attach($a, array("position"=>array_search(intval($a->id), $ids, true)));
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
		
		
		$hasDvrRecording = false;
		$liveStreamItem = !is_null($mediaItem) ? $mediaItem->liveStreamItem : null;
		if (!is_null($liveStreamItem)) {
			$hasDvrRecording = $liveStreamItem->dvrLiveStreamUris()->count() > 0;
		}
		
		$view = View::make('home.admin.media.edit');
		$view->editing = $editing;
		$view->streamOptions = $streamOptions;
		$view->form = $formData;
		$view->additionalForm = $additionalFormData;
		$view->hasDvrRecording = $hasDvrRecording;
		if ($hasDvrRecording) {
			$view->dvrRecordingRemoveUri = Config::get("custom.admin_base_url") . "/media/remove-dvr-recording/".$liveStreamItem->id;
		}
		$view->formErrors = $errors;
		// used to uniquely identify these file upload points on the site. Must not be duplicated for different upload points.
		$view->coverImageUploadPointId = Config::get("uploadPoints.coverImage");
		$view->sideBannersImageUploadPointId = Config::get("uploadPoints.sideBannersImage");
		$view->sideBannersFillImageUploadPointId = Config::get("uploadPoints.sideBannersFillImage");
		$view->coverArtUploadPointId = Config::get("uploadPoints.coverArt");
		$view->vodVideoUploadPointId = Config::get("uploadPoints.vodVideo");
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
							$mediaItem->sideBannerFillFile,
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
	
	public function postRemoveDvrRecording($mediaItemLiveStreamId) {
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 1);
		$resp = array("success"=>false);
		
		$mediaItemLiveStreamId = intval($mediaItemLiveStreamId, 10);
		$mediaItemLiveStream = MediaItemLiveStream::find($mediaItemLiveStreamId);
		if (!is_null($mediaItemLiveStream)) {
			$mediaItemLiveStream->removeDvrs();
			$resp["success"] = true;
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
			$mediaItems = MediaItem::with("playlists")->search($searchTerm)->orderBy("created_at", "desc")->take(20)->get();
		}
		else {
			$mediaItems = MediaItem::with("playlists")->orderBy("created_at", "desc")->take(20)->get();
		}
		
		$results = array();
		foreach($mediaItems as $a) {
			$results[] = array("id"=>intval($a->id), "text"=>$a->getNameWithInfo());
		}
		$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
		$resp['success'] = true;
		return Response::json($resp);
	}
	
	// ajax from the live stream control box on the player page on the main site
	public function postAdminStreamControlStreamState($id) {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 1);
		
		$mediaItem = MediaItem::with("liveStreamItem", "liveStreamItem.stateDefinition")->find($id);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$liveStreamItem = $mediaItem->liveStreamItem;
		if (is_null($liveStreamItem)) {
			App::abort(404);
		}
		
		// should receive stream_state which is the value that the stream_state should be set to.
		$requestedState = null;
		if (isset($_POST['stream_state'])) {
			$requestedState = intval($_POST['stream_state']);
		}
		
		$stateDefinition = LiveStreamStateDefinition::find($requestedState);
		if (is_null($stateDefinition)) {
			throw(new Exception("Invalid stream state."));
		}
		$liveStreamItem->stateDefinition()->associate($stateDefinition);
		$liveStreamItem->save();
	
		$resp = array("streamState"=> $liveStreamItem->stateDefinition->id);
		return Response::json($resp);
	}
	
	// ajax from the live stream control box on the player page on the main site
	public function postAdminStreamControlInfoMsg($id) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 1);
	
		$mediaItem = MediaItem::with("liveStreamItem", "liveStreamItem.stateDefinition")->find($id);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$liveStreamItem = $mediaItem->liveStreamItem;
		if (is_null($liveStreamItem)) {
			App::abort(404);
		}
		
		$requestedInfoMsg = null;
		if (isset($_POST['info_msg'])) {
			$requestedInfoMsg = $_POST['info_msg'];
		}
		if ($requestedInfoMsg === "") {
			$requestedInfoMsg = null;
		}

		if (!is_null($requestedInfoMsg) && strlen($requestedInfoMsg) <= 500) {
			$liveStreamItem->information_msg = $requestedInfoMsg;
			$liveStreamItem->save();
		}
		else if (is_null($requestedInfoMsg)) {
			$liveStreamItem->information_msg = null;
			$liveStreamItem->save();
		}
		
		$resp = array("infoMsg"=> $liveStreamItem->information_msg);
		return Response::json($resp);
	}

}
