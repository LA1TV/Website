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
use Csrf;
use EloquentHelpers;
use Auth;
use JsonHelpers;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\Series;

class PlaylistsController extends PlaylistsBaseController {

	public function getIndex() {
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
		
		$playlists = Playlist::with("series", "mediaItems")->search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($playlists as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			$noPlaylistItems = $a->mediaItems->count();
			$series = $a->series;
			$seriesStr = !is_null($series) ? $series->name . " (" . $a->series_no . ")" : "[Not Part Of Series]";
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> !is_null($a->name) ? $a->name : "[No Name]",
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"series"		=> $seriesStr,
				"noPlaylistItems"	=> $noPlaylistItems,
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/playlists/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/playlists/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/playlists/delete";
		$this->setContent($view, "playlists", "playlists");
	}
	
	public function anyEdit($id=null) {
		
		$playlist = null;
		$editing = false;
		if (!is_null($id)) {
			$playlist = Playlist::with("coverFile", "sideBannerFile", "coverArtFile", "mediaItems")->find($id);
			if (is_null($playlist)) {
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
			array("enabled", ObjectHelpers::getProp(false, $playlist, "enabled")?"y":""),
			array("series-id", ObjectHelpers::getProp("", $playlist, "series", "id")),
			array("series-no", ObjectHelpers::getProp("", $playlist, "series_no")),
			array("name", ObjectHelpers::getProp("", $playlist, "name")),
			array("description", ObjectHelpers::getProp("", $playlist, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $playlist, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $playlist, "sideBannerFile", "id")),
			array("cover-art-id", ObjectHelpers::getProp("", $playlist, "coverArtFile", "id")),
			array("publish-time", ObjectHelpers::getProp("", $playlist, "scheduled_publish_time_for_input")),
			array("playlist-content", json_encode(array()))
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$series = Series::find(intval($formData['series-id']));
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"coverArtFile"			=> FormHelpers::getFileInfo($formData['cover-art-id']),
			"seriesItemText"		=> !is_null($series) ? $series->name : "",
			"playlistContentInput"	=> null,
			"playlistContentInitialData"	=> null
		);
		
		if (!$formSubmitted) {
			$additionalFormData['playlistContentInput'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "playlist_content_for_input");
			$additionalFormData['playlistContentInitialData'] = ObjectHelpers::getProp(json_encode(array()), $playlist, "playlist_content_for_orderable_list");
		}
		else {
			$additionalFormData['playlistContentInput'] = MediaItem::generateInputValueForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["playlist-content"], true));
			$additionalFormData['playlistContentInitialData'] = MediaItem::generateInitialDataForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["playlist-content"], true));
		}
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('my_date', FormHelpers::getValidDateValidatorFunction());
			Validator::extend('valid_series_id', function($attribute, $value, $parameters) {
				return !is_null(Series::find(intval($value)));
			});
			Validator::extend('valid_playlist_content', function($attribute, $value, $parameters) {
				return MediaItem::isValidIdsFromAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
			});
			$modelCreated = DB::transaction(function() use (&$formData, &$playlist, &$errors) {
				
				$validator = Validator::make($formData,	array(
					'series-id'		=> array('valid_series_id'),
					'series-no'		=> array('required_with:series-id', 'integer'),
					'name'		=> array('required_without:series-id', 'max:50'),
					'description'	=> array('max:500'),
					'cover-image-id'	=> array('valid_file_id'),
					'side-banners-image-id'	=> array('valid_file_id'),
					'description'	=> array('max:500'),
					'cover-art-id'	=> array('valid_file_id'),
					'publish-time'	=> array('my_date'),
					'playlist-content'	=> array('required', 'valid_playlist_content')
				), array(
					'series-id.valid_series_id'	=> FormHelpers::getGenericInvalidMsg(),
					'series-no.required_with'	=> FormHelpers::getRequiredMsg(),
					'series-no.integer'	=> FormHelpers::getMustBeIntegerMsg(),
					'name.required_without'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'playlist-content.required'	=> FormHelpers::getGenericInvalidMsg(),
					'playlist-content.valid_playlist_content'	=> FormHelpers::getGenericInvalidMsg()
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($playlist)) {
						$playlist = new Playlist();
					}
					
					$playlist->name = FormHelpers::nullIfEmpty($formData['name']);
					$playlist->description = FormHelpers::nullIfEmpty($formData['description']);
					$playlist->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					$series = Series::find(intval($formData['series-id']));
					EloquentHelpers::associateOrNull($playlist->series(), $series);
					$playlist->series_no = !is_null($series) ? intval($formData['series-no']) : null;
					
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
					
					$playlist->mediaItems()->detach(); // detaches all
					$ids = json_decode($formData['playlist-content'], true);
					if (count($ids) > 0) {
						$mediaItems = MediaItem::whereIn("id", $ids)->get();
						foreach($mediaItems as $a) {
							$playlist->mediaItems()->attach($a, array("position"=>array_search(intval($a->id), $ids, true)));
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
		$view->seriesAjaxSelectDataUri = Config::get("custom.admin_base_url") . "/series/ajaxselect";
	
		$this->setContent($view, "playlists", "playlists-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
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
