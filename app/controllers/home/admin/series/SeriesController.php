<?php namespace uk\co\la1tv\website\controllers\home\admin\series;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use Csrf;
use DB;
use Validator;
use Redirect;
use uk\co\la1tv\website\models\Series;

class SeriesController extends SeriesBaseController {

	public function getIndex() {
		$view = View::make('home.admin.series.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noSeries = Series::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noSeries);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noPlaylists-1) {
			App::abort(404);
			return;
		}
		
		$series = Series::search($searchTerm)->usePagination()->orderBy("name", "asc")->orderBy("description", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($series as $a) {
			$enabled = (boolean) $a->enabled;
			$enabledStr = $enabled ? "Yes" : "No";
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"name"			=> $a->name,
				"description"	=> !is_null($a->description) ? $a->description : "[No Description]",
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/series/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/series/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/series/delete";
		$this->setContent($view, "series", "series");
	}
	
	public function anyEdit($id=null) {
		
		$series = null;
		$editing = false;
		if (!is_null($id)) {
			$series = Series::find($id);
			if (is_null($series)) {
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
			array("enabled", ObjectHelpers::getProp(false, $series, "enabled")?"y":""),
			array("name", ObjectHelpers::getProp("", $series, "name")),
			array("description", ObjectHelpers::getProp("", $series, "description"))
		), !$formSubmitted);
		
		
		$errors = null;
		
		if ($formSubmitted) {
			$modelCreated = DB::transaction(function() use (&$formData, &$series, &$errors) {
			
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
					if (is_null($series)) {
						$series = new Series();
					}
					
					$series->name = $formData['name'];
					$series->description = FormHelpers::nullIfEmpty($formData['description']);
					$series->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					if ($series->save() === false) {
						throw(new Exception("Error saving Series."));
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/series");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.series.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/series";
	
		$this->setContent($view, "series", "series-edit");
	}
	
	public function postDelete() {
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$series = Series::find($id);
				if (!is_null($series)) {
					if ($series->delete() === false) {
						throw(new Exception("Error deleting Series."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
