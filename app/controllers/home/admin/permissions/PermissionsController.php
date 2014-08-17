<?php namespace uk\co\la1tv\website\controllers\home\admin\permissions;

use Response;
use Csrf;
use Auth;
use FormHelpers;
use uk\co\la1tv\website\models\PermissionGroup;

class PermissionsController extends PermissionsBaseController {

	// json data for ajaxSelect element
	// route to this in routes.php
	public function handleGroupsAjaxSelect() {
		$resp = array("success"=>false, "payload"=>null);
		
		if (Csrf::hasValidToken() && Auth::isLoggedIn()) {
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
				$text = $a->name;
				if (!is_null($a->description)) {
					$text .= " (".$a->description.")";
				}
				$results[] = array("id"=>intval($a->id), "text"=>$text);
			}
			$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
			$resp['success'] = true;
		}
		return Response::json($resp);
	}
}
