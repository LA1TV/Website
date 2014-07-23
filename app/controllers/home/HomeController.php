<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use Auth;

class HomeController extends BaseController {

	public function getIndex() {
		return View::make('home.index');
	}

	// TODO: Temporary for debugging cosign
	public function getInfo() {
		var_dump($_ENV);
		
	}
}
