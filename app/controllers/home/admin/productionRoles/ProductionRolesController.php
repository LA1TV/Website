<?php namespace uk\co\la1tv\website\controllers\home\admin\productionRoles;

use Response;
use Auth;
use Config;
use FormHelpers;
use uk\co\la1tv\website\models\ProductionRoleMediaItem;

class ProductionRolesController extends ProductionRolesBaseController {

	// json data for ajaxSelect element
	public function postAjaxselect() {
		
		// TODO will need to be playlists for playlists version
		Auth::getUser()->hasPermissionOr401(Config::get("permissions.mediaItems"), 0);
	
		$resp = array("success"=>false, "payload"=>null);
		
		$searchTerm = FormHelpers::getValue("term", "");
		$qualities = null;
		if (!empty($searchTerm)) {
			$productionRoles = ProductionRoleMediaItem::with("productionRole")->search($searchTerm)->get();
		}
		else {
			$productionRoles = ProductionRoleMediaItem::get();
		}
		
		$positions = array();
		$results = array();
		foreach($productionRoles as $a) {
			$positions[] = intval($a->productionRole->position);
			$results[] = array("id"=>intval($a->id), "text"=>$a->getName());
		}
		
		// sort by position
		array_multisort($positions, SORT_ASC, SORT_NUMERIC, $results);
		
		$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
		$resp['success'] = true;
		return Response::json($resp);
	}
}
