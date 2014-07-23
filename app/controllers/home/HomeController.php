<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use View;

class HomeController extends BaseController {

	public function getIndex() {
		return View::make('home.index');
	}
}
