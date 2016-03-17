<?php namespace uk\co\la1tv\website\controllers\home\admin\productionRoles;

use Response;
use Auth;
use Config;
use FormHelpers;
use Exception;
use uk\co\la1tv\website\models\ProductionRoleMediaItem;
use uk\co\la1tv\website\models\ProductionRolePlaylist;

class ProductionRolesController extends ProductionRolesBaseController {

	// json data for ajaxSelect element
	public function postAjaxselect($type) {
		
		if ($type !== "mediaItem" && $type !== "playlist") {
			throw(new Exception("Invalid type."));
		}

		$permissionId = $type === "mediaItem" ? Config::get("permissions.mediaItems") : Config::get("permissions.playlists");
		Auth::getUser()->hasPermissionOr401($permissionId, 0);
	
		$resp = array("success"=>false, "payload"=>null);
		
		$model = $type === "mediaItem" ? new ProductionRoleMediaItem() : new ProductionRolePlaylist();

		$searchTerm = FormHelpers::getValue("term", "");
		$qualities = null;
		if (!empty($searchTerm)) {
			$productionRoles = $model->with("productionRole")->search($searchTerm)->get();
		}
		else {
			$productionRoles = $model->get();
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
