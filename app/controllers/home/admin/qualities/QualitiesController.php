<?php namespace uk\co\la1tv\website\controllers\home\admin\qualities;

use Response;
use Csrf;
use Auth;
use uk\co\la1tv\website\models\Quality

class QualitiesController extends QualitiesBaseController {

	// json data for ajaxSelect element
	// route to this in routes.php
	public function handleAjaxSelect() {
		$resp = array("success"=>false, "payload"=>null);
		
		if (Csrf::hasValidToken() && Auth::isLoggedIn()) {
			$searchTerm = FormHelpers::getValue("term", "");
			$qualities = null;
			if (!empty($searchTerm)) {
				$qualities = Quality::search($searchTerm)->orderBy("order", "asc")->get();
			}
			else {
				$qualities = Quality::orderBy("order", "asc")->get();
			}
			
			$results = array();
			foreach($qualities as $a) {
				$results[] = array("id"=>intval($a->id), "text"=>$a->name);
			}
			$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
			$resp['success'] = true;
		}
		return Response::json($resp);
	}
}
