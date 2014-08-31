<?php namespace uk\co\la1tv\website\controllers\home\show;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class ShowController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.show.index"), "show", "show");
	}
}
