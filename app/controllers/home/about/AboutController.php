<?php namespace uk\co\la1tv\website\controllers\home\about;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class AboutController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.about.index"), "about", "about");
	}
}
