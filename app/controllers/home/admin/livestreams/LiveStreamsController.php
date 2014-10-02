<?php namespace uk\co\la1tv\website\controllers\home\admin\livestreams;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use DB;
use Validator;
use Redirect;
use Response;
use Auth;
use App;
use JsonHelpers;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\QualityDefinition;

class LiveStreamsController extends LiveStreamsBaseController {

	public function getIndex() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.liveStreams"), 0);
	
		$view = View::make('home.admin.livestreams.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noLiveStreams = LiveStream::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noLiveStreams);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noLiveStreams-1) {
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
		$view->editEnabled = Auth::getUser()->hasPermission(Config::get("permissions.liveStreams"), 1);
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/livestreams/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/livestreams/delete";
		$this->setContent($view, "livestreams", "livestreams");
	}
	
	public function anyEdit($id=null) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.liveStreams"), 1);
		
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

		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $liveStream, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $liveStream, "name")),
			array("description", ObjectHelpers::getProp("", $liveStream, "description")),
			array("server-address", ObjectHelpers::getProp("", $liveStream, "server_address")),
			array("app-name", ObjectHelpers::getProp("", $liveStream, "app_name")),
			array("stream-name", ObjectHelpers::getProp("", $liveStream, "stream_name")),
			array("dvr-enabled", ObjectHelpers::getProp(false, $liveStream, "dvr_enabled")?"y":""),
			array("qualities", json_encode(array())),
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"qualitiesInput"		=> null,
			"qualitiesInitialData"	=> null
		);
		
		if (!$formSubmitted) {
			$additionalFormData['qualitiesInput'] = ObjectHelpers::getProp(json_encode(array()), $liveStream, "qualities_for_input");
			$additionalFormData['qualitiesInitialData'] = ObjectHelpers::getProp(json_encode(array()), $liveStream, "qualities_for_orderable_list");
		}
		else {
			$additionalFormData['qualitiesInput'] = QualityDefinition::generateInputValueForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData['qualities'], true));
			$additionalFormData['qualitiesInitialData'] = QualityDefinition::generateInitialDataForAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($formData["qualities"], true));
		}
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$liveStream, &$errors) {
				
				Validator::extend("valid_ip_or_domain", FormHelpers::getValidIPOrDomainFunction());
				Validator::extend('valid_qualities', function($attribute, $value, $parameters) {
					return QualityDefinition::isValidIdsFromAjaxSelectOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
				});
				
				$validator = Validator::make($formData,	array(
					'name'				=> array('required', 'max:50'),
					'description'		=> array('max:500'),
					'server-address'	=> array('required', 'max:50', 'valid_ip_or_domain'),
					'app-name'			=> array('required', 'max:50', 'alpha_dash'),
					'stream-name'		=> array('required', 'max:50', 'alpha_dash'),
					'qualities'			=> array('required', 'valid_qualities')
				), array(
					'name.required'			=> FormHelpers::getRequiredMsg(),
					'name.max'				=> FormHelpers::getLessThanCharactersMsg(50),
					'server-address.required'	=> FormHelpers::getRequiredMsg(),
					'server-address.max'	=> FormHelpers::getLessThanCharactersMsg(50),
					'server-address.valid_ip_or_domain'	=> FormHelpers::getInvalidIPOrDomainMsg(),
					'app-name.required'		=> FormHelpers::getRequiredMsg(),
					'app-name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'app-name.alpha_dash'	=> FormHelpers::getInvalidAlphaDashMsg(),
					'stream-name.required'	=> FormHelpers::getRequiredMsg(),
					'stream-name.max'		=> FormHelpers::getLessThanCharactersMsg(50),
					'stream-name.alpha_dash'	=> FormHelpers::getInvalidAlphaDashMsg(),
					'description.max'		=> FormHelpers::getLessThanCharactersMsg(500),
					'qualities.required'	=> FormHelpers::getGenericInvalidMsg(),
					'qualities.valid_qualities'	=> FormHelpers::getGenericInvalidMsg()
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
					$liveStream->app_name = $formData['app-name'];
					$liveStream->stream_name = $formData['stream-name'];
					
					if ($liveStream->save() === false) {
						throw(new Exception("Error saving LiveStream."));
					}
					
					$liveStream->qualities()->detach(); // detaches all
					$ids = json_decode($formData['qualities'], true);
					if (count($ids) > 0) {
						$qualities = QualityDefinition::whereIn("id", $ids)->get();
						foreach($qualities as $a) {
							$liveStream->qualities()->attach($a);
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/livestreams");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.livestreams.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->additionalForm = $additionalFormData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/livestreams";
	
		$this->setContent($view, "livestreams", "livestreams-edit");
	}
	
	public function postDelete() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.liveStreams"), 1);
	
		$resp = array("success"=>false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$liveStream = LiveStream::find($id);
				if (!is_null($liveStream)) {
					if ($liveStream->isDeletable()) {
						if ($liveStream->delete() === false) {
							throw(new Exception("Error deleting LiveStream."));
						}
						$resp['success'] = true;
					}
					else {
						$resp['msg'] = "This live stream cannot be deleted at the moment as it is being used in other places, or it's marked as currently being live.";
					}
				}
			});
		}
		return Response::json($resp);
	}
}
