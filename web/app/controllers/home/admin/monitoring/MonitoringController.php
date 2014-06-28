<?php namespace uk\co\la1tv\website\controllers\home\admin\monitoring;

use View;

class MonitoringController extends MonitoringBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.monitoring.index'), "monitoring", "monitoring");
	}
}
