<?php namespace uk\co\la1tv\website\controllers\home\liveStream;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class LiveStreamController extends HomeBaseController {

	public function getIndex($id) {
		$view = View::make("home.livestream.index");
		$openGraphProperties = array(); // TODO
		$this->setContent($view, "livestream", "livestream", $openGraphProperties, "TITLE TODO");
	}
	
	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && ctype_digit($parameters[0])) {
			return call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			return parent::missingMethod($parameters);
		}
	}
}
