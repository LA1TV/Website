<?php namespace uk\co\la1tv\website\controllers\home\guide;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class GuideController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.guide.index"), "guide", "guide");
	}
}
