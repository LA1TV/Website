<?php namespace uk\co\la1tv\website\controllers\home\admin\permissions;

use View;

class PermissionsController extends PermissionsBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.permissions.index'), "permissions", "permissions");
	}
}
