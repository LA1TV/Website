<?php namespace uk\co\la1tv\website\controllers\home\admin\livestreams;

use View;

class LiveStreamsController extends LiveStreamsBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.livestreams.index'), "livestreams", "livestreams");
	}
}
