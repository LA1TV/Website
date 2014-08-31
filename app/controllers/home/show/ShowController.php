<?php namespace uk\co\la1tv\website\controllers\home\show;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;

class ShowController extends HomeBaseController {

	public function getIndex($id) {
		$this->setContent(View::make("home.show.index"), "show", "show");
	}
	
	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && ctype_digit($parameters[0])) {
			call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			App::abort(404);
		}
	}
}
