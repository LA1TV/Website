<?php namespace uk\co\la1tv\website\controllers\home\admin\dashboard;

use View;

class DashboardController extends DashboardBaseController {
	
	public function getIndex() {
		$this->setContent(View::make('home.admin.dashboard.index'), "dashboard", "dashboard");
	}
}
