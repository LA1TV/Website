<?php namespace uk\co\la1tv\website\controllers\home\ajax;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use Response;

class AjaxController extends BaseController {

	public function postTime() {
		return Response::json(array(
			"time"	=> microtime(true)
		));
	}
	
	// used as an endpoint to ping to keep a users session alive
	public function postHello() {
		return Response::json(array(
			"data"	=> "hi"
		));
	}
}
