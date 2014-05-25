<?php namespace uk\co\la1tv\website\controllers\home\admin\users;

use View;

class UsersController extends UsersBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.users.index'), "users", "users");
	}
}
