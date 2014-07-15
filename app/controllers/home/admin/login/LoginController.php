<?php namespace uk\co\la1tv\website\controllers\home\admin\login;

use View;

class LoginController extends LoginBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.login.index'), "login", "login");
	}
}
