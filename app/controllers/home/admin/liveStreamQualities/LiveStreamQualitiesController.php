<?php namespace uk\co\la1tv\website\controllers\home\admin\liveStreamQualities;

use Response;
use Csrf;
use Auth;
use FormHelpers;
use uk\co\la1tv\website\models\LiveStreamQuality;

class LiveStreamQualitiesController extends LiveStreamQualitiesBaseController {

	// json data for ajaxSelect element
	public function postAjaxSelect() {
		$resp = array("success"=>false, "payload"=>null);
		
		if (Csrf::hasValidToken()) {
			$searchTerm = FormHelpers::getValue("term", "");
			$qualities = null;
			if (!empty($searchTerm)) {
				$qualities = LiveStreamQuality::with("qualityDefinition")->search($searchTerm)->orderBy("position", "asc")->get();
			}
			else {
				$qualities = LiveStreamQuality::with("qualityDefinition")->orderBy("position", "asc")->get();
			}
			$results = array();
			foreach($qualities as $a) {
				$results[] = array("id"=>intval($a->id), "text"=>$a->qualityDefinition->name);
			}
			$resp['payload'] = array("results"=>$results, "term"=>$searchTerm);
			$resp['success'] = true;
		}
		return Response::json($resp);
	}
}
