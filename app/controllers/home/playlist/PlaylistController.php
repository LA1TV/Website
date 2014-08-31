<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class PlaylistController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.playlist.index"), "playlist", "playlist");
	}
}
