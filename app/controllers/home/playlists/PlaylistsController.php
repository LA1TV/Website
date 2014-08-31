<?php namespace uk\co\la1tv\website\controllers\home\playlists;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class PlaylistsController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.playlists.index"), "playlists", "playlists");
	}
}
