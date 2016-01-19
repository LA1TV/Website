<?php namespace uk\co\la1tv\website\controllers\home\admin\apiUsers;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use DB;
use Validator;
use Redirect;
use Auth;
use Response;
use App;
use uk\co\la1tv\website\models\ApiUser;

class ApiUsersController extends ApiUsersBaseController {

	public function getIndex() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.apiUsers"), 0);
		
		$view = View::make('home.admin.apiUsers.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noUsers = ApiUser::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noUsers);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noUsers-1) {
			App::abort(404);
			return;
		}
		
		$apiUsers = ApiUser::search($searchTerm)->usePagination()->orderBy("enabled", "desc")->orderBy("owner", "asc")->orderBy("information", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($apiUsers as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			$canViewVodUris = (boolean) $a->can_view_vod_uris;
			$canViewVodUrisStr = $canViewVodUris ? "Yes" : "No";
			$canViewStreamUris = (boolean) $a->can_view_stream_uris;
			$canViewStreamUrisStr = $canViewStreamUris ? "Yes" : "No";
			$canUseWebhooks = (boolean) $a->can_use_webhooks;
			$canUseWebhooksStr = $canUseWebhooks ? "Yes" : "No";
			$lastRequestTime = !is_null($a->last_request_time) ? $a->last_request_time->toDateTimeString() : "[No Requests Yet]";

			$tableData[] = array(
				"enabled"				=> $enabledStr,
				"enabledCss"			=> $enabled ? "text-success" : "text-danger",
				"canViewVodUris"		=> $canViewVodUrisStr,
				"canViewVodUrisCss"		=> $canViewVodUris ? "text-success" : "text-danger",
				"canViewStreamUris"		=> $canViewStreamUrisStr,
				"canViewStreamUrisCss"	=> $canViewStreamUris ? "text-success" : "text-danger",
				"canUseWebhooks"		=> $canUseWebhooksStr,
				"canUseWebhooksCss"		=> $canUseWebhooks ? "text-success" : "text-danger",
				"owner"					=> $a->owner,
				"lastRequestTime"		=> $lastRequestTime,
				"timeCreated"			=> $a->created_at->toDateTimeString(),
				"editUri"				=> Config::get("custom.admin_base_url") . "/apiusers/edit/" . $a->id,
				"id"					=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->editEnabled = Auth::getUser()->hasPermission(Config::get("permissions.apiUsers"), 1);
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/apiusers/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/apiusers/delete";
		$this->setContent($view, "apiusers", "apiusers");
	}
	
	public function anyEdit($id=null) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.apiUsers"), 1);
		
		$apiUser = null;
		$editing = false;
		if (!is_null($id)) {
			$apiUser = ApiUser::find($id);
			if (is_null($apiUser)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(true, $apiUser, "enabled")?"y":""),
			array("can-view-vod-uris", ObjectHelpers::getProp(false, $apiUser, "can_view_vod_uris")?"y":""),
			array("can-view-stream-uris", ObjectHelpers::getProp(false, $apiUser, "can_view_stream_uris")?"y":""),
			array("can-use-webhooks", ObjectHelpers::getProp(false, $apiUser, "can_use_webhooks")?"y":""),
			array("owner", ObjectHelpers::getProp("", $apiUser, "owner")),
			array("information", ObjectHelpers::getProp("", $apiUser, "information")),
			array("key", ObjectHelpers::getProp(sha1(str_random(60)), $apiUser, "key"))
		), !$formSubmitted);
		
		$errors = null;
		
		if ($formSubmitted) {
		
			$modelCreated = DB::transaction(function() use (&$formData, &$apiUser, &$errors) {
				
				Validator::extend('unique_key', function($attribute, $value, $parameters) use (&$apiUser) {
					$currentId = !is_null($apiUser) ? intval($apiUser->id) : null;
					$q = ApiUser::where("key", $value);
					if (!is_null($currentId)) {
						$q = $q->where("id", "!=", $currentId);
					}
					return $q->count() === 0;
				});

				Validator::extend('unique_owner', function($attribute, $value, $parameters) use (&$apiUser) {
					$currentId = !is_null($apiUser) ? intval($apiUser->id) : null;
					$q = ApiUser::where("owner", $value);
					if (!is_null($currentId)) {
						$q = $q->where("id", "!=", $currentId);
					}
					return $q->count() === 0;
				});
				
				$validator = Validator::make($formData, array(
					'owner'			=> array('required', 'unique_owner'),
					'key'			=> array('required', 'unique_key', 'regex:/^[0-9a-f]{40}$/'),
					
				), array(
					'owner.required'			=> FormHelpers::getRequiredMsg(),
					'owner.unique_owner'		=> "There is already an api user with this owner.",
					'key.required'				=> FormHelpers::getGenericInvalidMsg(),
					'key.unique_key'			=> "This key is already in use.",
					'key.regex'					=> "The key must be a lower case SHA-1 hash.",
				));
				
				if (!$validator->fails()) {
					// everything is good. save model
					// build the model now. Then validate that there is at least one admin. Done in this order so that resultsInNoAccessibleAdminLogin() works.
					
					if (is_null($apiUser)) {
						$apiUser = new ApiUser();
					}
					
					$apiUser->enabled = FormHelpers::toBoolean($formData['enabled']);
					$apiUser->can_view_vod_uris = FormHelpers::toBoolean($formData['can-view-vod-uris']);
					$apiUser->can_view_stream_uris = FormHelpers::toBoolean($formData['can-view-stream-uris']);
					$apiUser->can_use_webhooks = FormHelpers::toBoolean($formData['can-use-webhooks']);
					$apiUser->owner = trim($formData['owner']);
					$apiUser->key = $formData['key'];
					$apiUser->information = FormHelpers::nullIfEmpty($formData['information']);
					
					if ($apiUser->save() === false) {
						throw(new Exception("Error saving ApiUser."));
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/apiusers");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.apiUsers.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/apiusers";
	
		$this->setContent($view, "apiusers", "apiusers-edit");
	}
	
	public function postDelete() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.apiUsers"), 1);
		
		$resp = array("success"=>false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$apiUser = ApiUser::find($id);
				if (!is_null($apiUser)) {
					if ($apiUser->delete() === false) {
						throw(new Exception("Error deleting ApiUser."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
