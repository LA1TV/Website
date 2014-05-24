<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use View;

class AdminController extends BaseController {

	public function getIndex() {
		return View::make('admin.index');
	}
}
