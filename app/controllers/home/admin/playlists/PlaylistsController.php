<?php namespace uk\co\la1tv\website\controllers\home\admin\playlists;

//TODO: update imports
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
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;

class PlaylistsController extends PlaylistsBaseController {

	public function getIndex() {
		$view = View::make('home.admin.playlists.index');
		
		$this->setContent($view, "playlists", "playlists");
	}
	
	public function anyEdit($id=null) {
		
		$playlist = null;
		$editing = false;
		if (!is_null($id)) {
			//TODO: add with
			$playlist = Playlist::with("coverFile", "sideBannerFile", "coverArtFile")->find($id);
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
		// TODO: series
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $playlist, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $playlist, "name")),
			array("description", ObjectHelpers::getProp("", $playlist, "description")),
			array("cover-image-id", ObjectHelpers::getProp("", $playlist, "coverFile", "id")),
			array("side-banners-image-id", ObjectHelpers::getProp("", $playlist, "sideBannerFile", "id")),
			array("cover-art-id", ObjectHelpers::getProp("", $playlist, "coverArtFile", "id")),
			array("publish-time", ObjectHelpers::getProp("", $playlist, "scheduled_publish_time_for_input")),
			array("playlist-content", ObjectHelpers::getProp("", $playlist, "playlist_content_for_input"))
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"coverImageFile"		=> FormHelpers::getFileInfo($formData['cover-image-id']),
			"sideBannersImageFile"	=> FormHelpers::getFileInfo($formData['side-banners-image-id']),
			"coverArtFile"			=> FormHelpers::getFileInfo($formData['cover-art-id'])
		);
		
		$errors = null;
		
		if ($formSubmitted) {
			// validate input
			Validator::extend('valid_file_id', FormHelpers::getValidFileValidatorFunction());
			Validator::extend('my_date', FormHelpers::getValidDateValidatorFunction());
			Validator::extend('valid_playlist_content', function($attribute, $value, $parameters) {
				$ids = explode(",", $value);
				if (count($ids) === 0) {
					return true;
				}
				return MediaItem::whereIn("id", $ids)->count() === count($ids);
			});
			
			$modelCreated = DB::transaction(function() use (&$formData, &$mediaItem, &$errors) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500'),
					'cover-image-id'	=> array('valid_file_id'),
					'side-banners-image-id'	=> array('valid_file_id'),
					'description'	=> array('max:500'),
					'cover-art-id'	=> array('valid_file_id'),
					'publish-time'	=> array('my_date'),
					'playlist-content'	=> array('valid_playlist_content')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500),
					'cover-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'side-banners-image-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'cover-art-id.valid_file_id'	=> FormHelpers::getInvalidFileMsg(),
					'publish-time.my_date'	=> FormHelpers::getInvalidTimeMsg(),
					'playlist-content.valid_playlist_content'	=> FormHelpers::getGenericInvalidMsg()
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($playlist)) {
						$playlist = new Playlist();
					}
					
					$playlist->name = $formData['name'];
					$playlist->description = FormHelpers::nullIfEmpty($formData['description']);
					$playlist->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					$coverImageId = FormHelpers::nullIfEmpty($formData['cover-image-id']);
					$file = Upload::register(Config::get("uploadPoints.coverImage"), $coverImageId, $playlist->coverFile);
					EloquentHelpers::associateOrNull($playlist->coverFile(), $file);
					
					$sideBannerFileId = FormHelpers::nullIfEmpty($formData['side-banners-image-id']);
					$file = Upload::register(Config::get("uploadPoints.sideBannersImage"), $sideBannerFileId, $playlist->sideBannerFile);
					EloquentHelpers::associateOrNull($playlist->sideBannerFile(), $file);
					
					$coverArtFileId = FormHelpers::nullIfEmpty($formData['cover-art-id']);
					$file = Upload::register(Config::get("uploadPoints.coverArt"), $coverArtFileId, $playlist->coverArtFile);
					EloquentHelpers::associateOrNull($playlist->coverArtFile(), $file);
					
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
		$view->cancelUri = Config::get("custom.admin_base_url") . "/playlist";
	
		$this->setContent($view, "playlist", "playlist-edit");
	}
	
	// TODO
	public function postDelete() {
	return;
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
}
