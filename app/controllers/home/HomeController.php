<?php namespace uk\co\la1tv\website\controllers\home;

use View;

class HomeController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.index"), "home", "home");
	}
}
