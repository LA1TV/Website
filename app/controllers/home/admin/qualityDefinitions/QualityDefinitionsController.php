<?php namespace uk\co\la1tv\website\controllers\home\admin\qualityDefinitions;

use Response;
use Auth;
use Config;
use FormHelpers;
use uk\co\la1tv\website\models\QualityDefinition;

class QualityDefinitionsController extends QualityDefinitionsBaseController {

	// json data for ajaxSelect element
	public function postAjaxselect() {
		
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.liveStreams"), 0);
	
		$resp = array("success"=>false, "payload"=>null);
		
		$searchTerm = FormHelpers::getValue("term", "");
		$qualities = null;
		if (!empty($searchTerm)) {
			$qualities = QualityDefinition::search($searchTerm)->orderBy("position")->get();
		}
		else {
			$qualities = QualityDefinition::orderBy("position")->get();
		}
		$results = array();
		foreach($qualities as $a) {
			$results[] = array("id"=>intval($a->id), "text"=>$a->name);
		}
		
		$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
		$resp['success'] = true;
		return Response::json($resp);
	}
}
