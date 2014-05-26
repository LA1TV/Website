<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;

class UploadController extends UploadBaseController {

	public function postIndex() {
		
		$resp = array(
			"success"	=> true,
			"id"		=> 1
		);
	
		return Response::json($resp);
	}
}
