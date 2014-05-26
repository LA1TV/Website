<?php namespace uk\co\la1tv\website\controllers\home\admin\media;

use View;

class MediaController extends MediaBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.media.index'), "media", "media");
	}
	
	public function getEdit() {
		$this->setContent(View::make('home.admin.media.edit'), "media", "media-edit");
	}
}
