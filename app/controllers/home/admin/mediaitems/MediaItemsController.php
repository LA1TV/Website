<?php namespace uk\co\la1tv\website\controllers\home\admin\mediaitems;

use View;

class MediaItemsController extends MediaItemsBaseController {

	public function getIndex() {
		$this->setContent(View::make('admin.media'), "media", "media");
	}
}
