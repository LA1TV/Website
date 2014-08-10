<?php namespace uk\co\la1tv\website\controllers\home\admin\livestreams;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use Csrf;
use DB;
use Validator;
use Redirect;
use Response;
use Auth;
use uk\co\la1tv\website\models\LiveStream;

class LiveStreamsController extends LiveStreamsBaseController {

	public function getIndex() {
		$view = View::make('home.admin.livestreams.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noLiveStreams = LiveStream::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noLiveStreams);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noPlaylists-1) {
			App::abort(404);
			return;
		}
		
		$liveStreams = LiveStream::search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($liveStreams as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			$hasDvr = (boolean) $a->dvr_enabled;
			$hasDvrStr = $hasDvr ? "Yes" : "No";
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"hasDvr"		=> $hasDvrStr,
				"hasDvrCss"		=> $hasDvr ? "text-success" : "text-danger",
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/livestreams/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/livestreams/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/livestreams/delete";
		$this->setContent($view, "livestreams", "livestreams");
	}
	
	public function anyEdit($id=null) {
		
		$liveStream = null;
		$editing = false;
		if (!is_null($id)) {
			$liveStream = LiveStream::find($id);
			if (is_null($liveStream)) {
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
			array("enabled", ObjectHelpers::getProp(false, $liveStream, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $liveStream, "name")),
			array("description", ObjectHelpers::getProp("", $liveStream, "description")),
			array("server-address", ObjectHelpers::getProp("", $liveStream, "server_address")),
			array("stream-name", ObjectHelpers::getProp("", $liveStream, "stream_name")),
			array("dvr-enabled", ObjectHelpers::getProp(false, $liveStream, "dvr_enabled")?"y":""),
		), !$formSubmitted);
		
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$liveStream, &$errors) {
				
				Validator::extend("valid_domain", FormHelpers::getValidDomainValidatorFunction());
				
				$validator = Validator::make($formData,	array(
					'name'				=> array('required', 'max:50'),
					'description'		=> array('max:500'),
					'server-address'	=> array('required', 'max:50', 'valid_domain'),
					'stream-name'		=> array('required', 'max:50', 'alpha_dash'),
				), array(
					'name.required'			=> FormHelpers::getRequiredMsg(),
					'name.max'				=> FormHelpers::getLessThanCharactersMsg(50),
					'server-address.required'	=> FormHelpers::getRequiredMsg(),
					'server-address.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'server-address.valid_domain'	=> FormHelpers::getInvalidDomainMsg(),
					'stream-name.required'	=> FormHelpers::getRequiredMsg(),
					'stream-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
					'stream-name.alpha_dash'	=> FormHelpers::getInvalidAlphaDashMsg(),
					'description.max'		=> FormHelpers::getLessThanCharactersMsg(500)
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($liveStream)) {
						$liveStream = new LiveStream();
					}
					
					$liveStream->name = $formData['name'];
					$liveStream->description = FormHelpers::nullIfEmpty($formData['description']);
					$liveStream->enabled = FormHelpers::toBoolean($formData['enabled']);
					$liveStream->dvr_enabled = FormHelpers::toBoolean($formData['dvr-enabled']);
					$liveStream->server_address = $formData['server-address'];
					$liveStream->stream_name = $formData['stream-name'];
					
					if ($liveStream->save() === false) {
						throw(new Exception("Error saving LiveStream."));
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/livestreams");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.livestreams.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/livestreams";
	
		$this->setContent($view, "livestreams", "livestreams-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$liveStream = LiveStream::find($id);
				if (!is_null($liveStream)) {
					if ($liveStream->delete() === false) {
						throw(new Exception("Error deleting LiveStream."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
