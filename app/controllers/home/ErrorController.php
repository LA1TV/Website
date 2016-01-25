<?php namespace uk\co\la1tv\website\controllers\home;

use View;
use URL;

class ErrorController extends HomeBaseController {

	public function generate404() {
		$view = View::make("home.errors.404");
		$view->homeUrl = URL::to("/");
		$this->setContent($view, "error", "error-404", array(), "Error 404- Content Not Found", 404);
	}
}
