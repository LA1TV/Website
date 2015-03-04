<?php namespace uk\co\la1tv\website\controllers\home\admin\playlists;

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
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\CustomUri;

class PlaylistsController extends PlaylistsBaseController {

	public function getIndex() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.playlists"), 0);
	
		$view = View::make('home.admin.playlists.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noPlaylists = Playlist::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noPlaylists);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noPlaylists-1) {
			App::abort(404);
			return;
		}
		
		$playlists = Playlist::with("show", "mediaItems", "customUri")->search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($playlists as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			$noPlaylistItems = $a->mediaItems->count();
			$show = $a->show;
			$showStr = !is_null($show) ? $show->name . " (" . $a->series_no . ")" : "[Not Part Of Show]";
			$customUri = $a->custom_uri_name;
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> !is_null($a->name) ? $a->name : "[No Name]",
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"show"			=> $showStr,
				"noPlaylistItems"	=> $noPlaylistItems,
				"customUri"		=> !is_null($customUri) ? $customUri : "[No Custom URI]",
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/playlists/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->editEnabled = Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 1);
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/playlists/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/playlists/delete";
		$this->setContent($view, "playlists", "playlists");
	}
	
	public function anyEdit($id=null) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.playlists"), 1);
		
		$playlist = null;
		$editing = false;
		if (!is_null($id)) {
			$playlist = Playlist::with("coverFile", "sideBannerFile", "coverArtFile", "mediaItems", "customUri")->find($id);
			if (is_null($playlist)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
	
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $playlist, "enabled")?"y":""),
			array("show-id", ObjectHelpers::getProp("", $playlist, "show", "id")),
			array("series-no", ObjectHelpers::getProp("", $playlist, "series_no")),
			array("name", ObjectHelpers::getProp("", $playlist, "name")),
			array("description", ObjectHelpers::getProp("", $playlist, "description")),
			array("custom-uri", ObjectHelpers::getProp("", $playlist, "custom_uri_name")),
			array("cover-image-id", ObjectHelpers::getProp("", $playlist, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $playlist, "sideBannerFile", "id")),
			array("cover-art-id", ObjectHelpers::getProp("", $playlist, "coverArtFile", "id")),
			array("publish-time", ObjectHelpers::getProp("", $playlist, "scheduled_publish_time_for_input")),
			array("playlist-content", json_encode(array())),
			array("related-items", json_encode(array())),
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$show = Show::find(intval($formData['show-id']));
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"coverArtFile"			=> FormHelpers::getFileInfo($formData['cover-art-id']),
			"showItemText"			=> !is_null($show) ? $show->name : "",
			"playlistContentInput"	=> null,
			"playlistContentInitialData"	=> null,
			"relatedItemsInput"		=> null,
			"relatedItemsInitialData"	=> null
		);
		
		if (!$formSubmitted) {
			$additionalFormData['playlistContentInput'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "playlist_content_for_input");
			$additionalFormData['playlistContentInitialData'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "playlist_content_for_orderable_list");
			$additionalFormData['relatedItemsInput'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "related_items_for_input");
			$additionalFormData['relatedItemsInitialData'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "related_items_for_orderable_list");
		}
		else {
			$additionalFormData['playlistContentInput'] = MediaItem::generateInputValueForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["playlist-content"], true));
			$additionalFormData['playlistContentInitialData'] = MediaItem::generateInitialDataForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["playlist-content"], true));
			$additionalFormData['relatedItemsInput'] = MediaItem::generateInputValueForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["related-items"], true));
			$additionalFormData['relatedItemsInitialData'] = MediaItem::generateInitialDataForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["related-items"], true));
		}
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('my_date', FormHelpers::getValidDateValidatorFunction());
			Validator::extend('valid_show_id', function($attribute, $value, $parameters) {
				return !is_null(Show::find(intval($value)));
			});
			Validator::extend('valid_playlist_content', function($attribute, $value, $parameters) {
				return MediaItem::isValidIdsFromAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
			});
			Validator::extend('valid_related_items', function($attribute, $value, $parameters) {
				return MediaItem::isValidIdsFromAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
			});
			Validator::extend('unique_custom_uri', function($attribute, $value, $parameters) use (&$playlist) {
				$q = CustomUri::where("name", $value);
				if (!is_null($playlist)) {
					$currentCustomUri = $playlist->custom_uri_name;
					if (!is_null($currentCustomUri)) {
						$q = $q->where("name", "!=", $currentCustomUri);
					}
				}
				return $q->count() === 0;
			});
			
			$modelCreated = DB::transaction(function() use (&$formData, &$playlist, &$errors) {
				
				$validator = Validator::make($formData,	array(
					'show-id'		=> array('valid_show_id'),
					'series-no'		=> array('required_with:show-id', 'integer'),
					'name'		=> array('required_without:show-id', 'max:50'),
					'description'	=> array('max:500'),
					'custom-uri'		=> array('alpha_dash', 'max:50', 'unique_custom_uri'),
					'cover-image-id'	=> array('valid_file_id'),
					'side-banners-image-id'	=> array('valid_file_id'),
					'description'	=> array('max:500'),
					'cover-art-id'	=> array('valid_file_id'),
					'publish-time'	=> array('my_date'),
					'playlist-content'	=> array('required', 'valid_playlist_content'),
					'related-items'	=> array('required', 'valid_related_items')
				), array(
					'show-id.valid_show_id'	=> FormHelpers::getGenericInvalidMsg(),
					'series-no.required_with'	=> FormHelpers::getRequiredMsg(),
					'series-no.integer'	=> FormHelpers::getMustBeIntegerMsg(),
					'name.required_without'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'custom-uri.alpha_dash'	=> FormHelpers::getInvalidAlphaDashMsg(),
					'custom-uri.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'custom-uri.unique_custom_uri'	=> "This is already in use.",
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'playlist-content.required'	=> FormHelpers::getGenericInvalidMsg(),
					'playlist-content.valid_playlist_content'	=> FormHelpers::getGenericInvalidMsg(),
					'related-items.required'	=> FormHelpers::getGenericInvalidMsg(),
					'related-items.valid_related_items'	=> FormHelpers::getGenericInvalidMsg()
				));
				
				if (!$validator->fails()) {
				
					$show = $formData['show-id'] !== "" ? Show::find(intval($formData['show-id'])) : null;
					
					Validator::extend('unique_series_no', function($attribute, $value, $parameters) use (&$playlist, &$show) {
						if (is_null($show)) {
							return true;
						}
						$count = $show->playlists()->where("series_no", $value);
						if (!is_null($playlist)) {
							$count = $count->where("id", "!=", $playlist->id);
						}
						$count = $count->count();
						return $count === 0;
					});
					
					$validator = Validator::make($formData,	array(
						'series-no'		=> array('unique_series_no')
					), array(
						'series-no.unique_series_no'	=> "A series already exists with that number."
					));
						
					if (!$validator->fails()) {
					
					
						// everything is good. save/create model
						if (is_null($playlist)) {
							$playlist = new Playlist();
						}
						
						$playlist->name = FormHelpers::nullIfEmpty($formData['name']);
						$playlist->description = FormHelpers::nullIfEmpty($formData['description']);
						$playlist->enabled = FormHelpers::toBoolean($formData['enabled']);
						
						// if the scheduled publish time is empty and this playlist is enabled, set it to the current time.
						// an enabled playlist should always have a published time.
						$scheduledPublishTime = FormHelpers::nullIfEmpty(strtotime($formData['publish-time']));
						$playlist->scheduled_publish_time = !is_null($scheduledPublishTime) ? $scheduledPublishTime : Carbon::now();
						
						
						EloquentHelpers::associateOrNull($playlist->show(), $show);
						$playlist->series_no = !is_null($show) ? intval($formData['series-no']) : null;
						
						$coverImageId = FormHelpers::nullIfEmpty($formData['cover-image-id']);
						$file = Upload::register(Config::get("uploadPoints.coverImage"), $coverImageId, $playlist->coverFile);
						EloquentHelpers::associateOrNull($playlist->coverFile(), $file);
						
						$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
						$file = Upload::register(Config::get("uploadPoints.sideBannersImage"), $sideBannerFileId, $playlist->sideBannerFile);
						EloquentHelpers::associateOrNull($playlist->sideBannerFile(), $file);
						
						$coverArtFileId = FormHelpers::nullIfEmpty($formData['cover-art-id']);
						$file = Upload::register(Config::get("uploadPoints.coverArt"), $coverArtFileId, $playlist->coverArtFile);
						EloquentHelpers::associateOrNull($playlist->coverArtFile(), $file);
						
						if ($playlist->save() === false) {
							throw(new Exception("Error saving Playlist."));
						}
						
						$customUri = FormHelpers::nullIfEmpty($formData['custom-uri']);
						$currentCustomUriModel = $playlist->customUri;
						if (!is_null($customUri)) {
							if ($playlist->custom_uri_name !== $customUri) {
								// change needed
								if (!is_null($currentCustomUriModel)) {
									// remove the current one first
									$currentCustomUriModel->delete();
								}
								$customUriModel = new CustomUri(array("name"=>$customUri));
								$playlist->customUri()->save($customUriModel);
							}
						}
						else {
							if (!is_null($currentCustomUriModel)) {
								// remove the current one
								$currentCustomUriModel->delete();
							}
						}
						
						$playlist->mediaItems()->detach(); // detaches all
						$ids = json_decode($formData['playlist-content'], true);
						if (count($ids) > 0) {
							$mediaItems = MediaItem::whereIn("id", $ids)->get();
							foreach($mediaItems as $a) {
								$playlist->mediaItems()->attach($a, array("position"=>array_search(intval($a->id), $ids, true)));
							}
						}
						
						$playlist->relatedItems()->detach(); // detaches all
						$ids = json_decode($formData['related-items'], true);
						if (count($ids) > 0) {
							$mediaItems = MediaItem::whereIn("id", $ids)->get();
							foreach($mediaItems as $a) {
								$playlist->relatedItems()->attach($a, array("position"=>array_search(intval($a->id), $ids, true)));
							}
						}

						// the transaction callback result is returned out of the transaction function
						return true;
					}
					else {
						$errors = $validator->messages();
						return false;
					}
				}
				else {
					$errors = $validator->messages();
					return false;
				}
			});
			
			if ($modelCreated) {
				return Redirect::to(Config::get("custom.admin_base_url") . "/playlists");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.playlists.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->additionalForm = $additionalFormData;
		$view->formErrors = $errors;
		// used to uniquely identify these file upload points on the site. Must not be duplicated for different upload points.
		$view->coverImageUploadPointId = Config::get("uploadPoints.coverImage");
		$view->sideBannersImageUploadPointId = Config::get("uploadPoints.sideBannersImage");
		$view->coverArtUploadPointId = Config::get("uploadPoints.coverArt");
		$view->cancelUri = Config::get("custom.admin_base_url") . "/playlists";
		$view->seriesAjaxSelectDataUri = Config::get("custom.admin_base_url") . "/shows/ajaxselect";
	
		$this->setContent($view, "playlists", "playlists-edit");
	}
	
	public function postDelete() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.playlists"), 1);
		
		$resp = array("success"=>false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$playlist = Playlist::find($id);
				if (!is_null($playlist)) {
					// mark any related files as no longer in use (so they will be removed)
					Upload::delete(array(
						$playlist->sideBannerFile,
						$playlist->coverFile,
						$playlist->coverArtFile
					));
					
					$customUriModel = $playlist->customUri;
					if (!is_null($customUriModel)) {
						$customUriModel->delete();
					}
					
					if ($playlist->delete() === false) {
						throw(new Exception("Error deleting Playlist."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
