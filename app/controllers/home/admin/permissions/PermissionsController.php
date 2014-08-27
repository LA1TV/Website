<?php namespace uk\co\la1tv\website\controllers\home\admin\permissions;

use Response;
use Auth;
use FormHelpers;
use uk\co\la1tv\website\models\PermissionGroup;

class PermissionsController extends PermissionsBaseController {

	// json data for ajaxSelect element
	public function postGroupsajaxselect() {
		$resp = array("success"=>false, "payload"=>null);
		
		$searchTerm = FormHelpers::getValue("term", "");
		$groups = null;
		if (!empty($searchTerm)) {
			$groups = PermissionGroup::search($searchTerm)->orderBy("position", "asc")->get();
		}
		else {
			$groups = PermissionGroup::orderBy("position", "asc")->get();
		}
		$results = array();
		foreach($groups as $a) {
			$results[] = array("id"=>intval($a->id), "text"=>$a->getNameAndDescription());
		}
		$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
		$resp['success'] = true;
		return Response::json($resp);
	}
}
