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
use uk\co\la1tv\website\models\LiveStreamUri;
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
			array("urls", json_encode(array())),
		), !$formSubmitted);
		
		// this will contain any additional data which does not get saved anywhere
		$additionalFormData = array(
			"urlsInput"			=> null,
			"urlsInitialData"	=> null
		);
		
		if (!$formSubmitted) {
			$additionalFormData['urlsInput'] = ObjectHelpers::getProp(json_encode(array()), $liveStream, "urls_for_input");
			$additionalFormData['urlsInitialData'] = ObjectHelpers::getProp(json_encode(array()), $liveStream, "urls_for_orderable_list");
		}
		else {
			$additionalFormData['urlsInput'] = LiveStream::generateInputValueForUrlsOrderableList(JsonHelpers::jsonDecodeOrNull($formData['urls'], true));
			$additionalFormData['urlsInitialData'] = LiveStream::generateInitialDataForUrlsOrderableList(JsonHelpers::jsonDecodeOrNull($formData["urls"], true));
		}
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$liveStream, &$errors) {
				
				Validator::extend('valid_urls', function($attribute, $value, $parameters) {
					return LiveStream::isValidDataFromUrlsOrderableList(JsonHelpers::jsonDecodeOrNull($value, true));
				});
				
				$validator = Validator::make($formData,	array(
					'name'				=> array('required', 'max:50'),
					'description'		=> array('max:500'),
					'urls'				=> array('required', 'valid_urls')
				), array(
					'name.required'			=> FormHelpers::getRequiredMsg(),
					'name.max'				=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'		=> FormHelpers::getLessThanCharactersMsg(500),
					'urls.required'			=> FormHelpers::getGenericInvalidMsg(),
					'urls.valid_urls'		=> FormHelpers::getGenericInvalidMsg()
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($liveStream)) {
						$liveStream = new LiveStream();
					}
					
					$liveStream->name = $formData['name'];
					$liveStream->description = FormHelpers::nullIfEmpty($formData['description']);
					$liveStream->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					if ($liveStream->save() === false) {
						throw(new Exception("Error saving LiveStream."));
					}
					
					$liveStream->liveStreamUris()->delete(); // detaches all
					$urlsData = json_decode($formData['urls'], true);
					foreach($urlsData as $a) {
						$qualityDefinition = QualityDefinition::find(intval($a['qualityState']['id']));
						$url = $a['url'];
						$type = $a['type'];
						$support = $a['support'];
						$supportedDevices = null;
						if ($support === "pc") {
							$supportedDevices = "pc";
						}
						else if ($support === "mobile") {
							$supportedDevices = "mobile";
						}
						$liveStreamUri = new LiveStreamUri(array(
							"uri"				=> $url,
							"type"				=> $type,
							"supported_devices"	=> $supportedDevices
						));
						$liveStreamUri->qualityDefinition()->associate($qualityDefinition);
						$liveStream->liveStreamUris()->save($liveStreamUri);
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
