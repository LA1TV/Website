<?php namespace uk\co\la1tv\website\controllers\home\admin\siteUsers;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use Csrf;
use DB;
use Redirect;
use Response;
use Auth;
use uk\co\la1tv\website\models\SiteUser;

class SiteUsersController extends SiteUsersBaseController {

	public function getIndex() {
		$view = View::make('home.admin.siteUsers.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noSiteUsers = SiteUser::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noSiteUsers);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noSiteUsers-1) {
			App::abort(404);
			return;
		}
		
		$siteUsers = SiteUser::search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("email", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($siteUsers as $a) {
			$banned = (boolean) $a->banned;
			$bannedStr = $banned ? "Yes" : "No";
			
			$tableData[] = array(
				"banned"		=> $bannedStr,
				"bannedCss"		=> $banned ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"email"			=> $a->email,
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/siteusers/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/siteusers/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/siteusers/delete";
		$this->setContent($view, "siteusers", "siteusers");
	}
	
	public function anyEdit($id) {
		
		$siteUser = SiteUser::find($id);
		if (is_null($siteUser)) {
			App::abort(404);
			return;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
	
		if ($formSubmitted) {
			// throws exception if token invalid
			Csrf::check();
		};
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("banned", ObjectHelpers::getProp(false, $siteUser, "banned")?"y":"")
		), !$formSubmitted);
		
		$additionalFormData = array(
			"name"		=> $siteUser->name,
			"firstName"		=> $siteUser->first_name,
			"lastName"	=> $siteUser->last_name,
			"email"		=> $siteUser->email
		);
		
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$siteUser, &$errors) {
			
				// everything is good. save/create model
				$siteUser->banned = FormHelpers::toBoolean($formData['banned']);	
				if ($siteUser->save() === false) {
					throw(new Exception("Error saving SiteUser."));
				}
				// the transaction callback result is returned out of the transaction function
				return true;
			});
			
			if ($modelCreated) {
				return Redirect::to(Config::get("custom.admin_base_url") . "/siteusers");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.siteUsers.edit');
		$view->form = $formData;
		$view->additionalForm = $additionalFormData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/siteusers";
	
		$this->setContent($view, "siteusers", "siteusers-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$siteUser = SiteUser::find($id);
				if (!is_null($siteUser)) {
					if ($siteUser->delete() === false) {
						throw(new Exception("Error deleting SiteUser."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
