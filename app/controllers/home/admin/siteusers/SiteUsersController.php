<?php namespace uk\co\la1tv\website\controllers\home\admin\siteusers;

use View;

class SiteUsersController extends SiteUsersBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.siteusers.index'), "siteusers", "siteusers");
	}
}
