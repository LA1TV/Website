<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use DebugHelpers;

class ApiController extends ApiBaseController {

	public function getService() {
		$data = [
			"apiVersion"			=> 1,
			"applicationVersion"	=> DebugHelpers::getVersion()
		];
		return $this->respond($data);
	}
	
	public function getShows() {
		// TODO
		return $this->respondNotFound();
	}
	
	public function getShow($id) {
		// TODO
		return $this->respondNotFound();
	}
	
	public function getPlaylists() {
		// TODO
		return $this->respondNotFound();
	}
	
	public function getPlaylist($id) {
		// TODO
		return $this->respondNotFound();
	}
	
	public function getMediaItem($id) {
		// TODO
		return $this->respondNotFound();
	}
}
