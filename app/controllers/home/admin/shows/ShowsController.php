<?php namespace uk\co\la1tv\website\controllers\home\admin\shows;

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
use uk\co\la1tv\website\models\Show;

class ShowsController extends ShowsBaseController {

	public function getIndex() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.shows"), 0);
	
		$view = View::make('home.admin.shows.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noShows = Show::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noShows);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noShows-1) {
			App::abort(404);
			return;
		}
		
		$shows = Show::search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($shows as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/shows/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->editEnabled = Auth::getUser()->hasPermission(Config::get("permissions.shows"), 1);
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/shows/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/shows/delete";
		$this->setContent($view, "shows", "shows");
	}
	
	public function anyEdit($id=null) {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.shows"), 1);
		
		$show = null;
		$editing = false;
		if (!is_null($id)) {
			$show = Show::find($id);
			if (is_null($show)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(false, $show, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $show, "name")),
			array("description", ObjectHelpers::getProp("", $show, "description"))
		), !$formSubmitted);
		
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$show, &$errors) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500)
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($show)) {
						$show = new Show();
					}
					
					$show->name = $formData['name'];
					$show->description = FormHelpers::nullIfEmpty($formData['description']);
					$show->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					if ($show->save() === false) {
						throw(new Exception("Error saving Show."));
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/shows");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.shows.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/shows";
	
		$this->setContent($view, "shows", "shows-edit");
	}
	
	public function postDelete() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.shows"), 1);
	
		$resp = array("success"=>false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$show = Show::find($id);
				if (!is_null($show)) {
					if ($show->isDeletable()) {
						if ($show->delete() === false) {
							throw(new Exception("Error deleting Show."));
						}
						$resp['success'] = true;
					}
					else {
						$resp['msg'] = "This show cannot be deleted at the moment as it is being used in other places.";
					}
				}
			});
		}
		return Response::json($resp);
	}

	// route to this in routes.php
	public function postAjaxselect() {
	
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.shows"), 0);
		
		$resp = array("success"=>false, "payload"=>null);
		
		$searchTerm = FormHelpers::getValue("term", "");
		$shows = null;
		if (!empty($searchTerm)) {
			$shows = Show::search($searchTerm)->orderBy("created_at", "desc")->take(20)->get();
		}
		else {
			$shows = Show::orderBy("created_at", "desc")->take(20)->get();
		}
		
		$results = array();
		foreach($shows as $a) {
			$results[] = array("id"=>intval($a->id), "text"=>$a->name);
		}
		$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
		$resp['success'] = true;
		return Response::json($resp);
	}
}
