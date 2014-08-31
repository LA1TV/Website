<?php namespace uk\co\la1tv\website\controllers\home\shows;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class ShowsController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.shows.index"), "shows", "shows");
	}
}
