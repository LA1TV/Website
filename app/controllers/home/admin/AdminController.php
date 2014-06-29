<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use Redirect;

class AdminController extends BaseController {
	
	public function getIndex() {
		return Redirect::to('admin/dashboard');
	}
}
