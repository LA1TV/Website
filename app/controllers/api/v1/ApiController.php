<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use uk\co\la1tv\website\transformers\ShowTransformer;
use uk\co\la1tv\website\transformers\PlaylistTransformer;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use DebugHelpers;

class ApiController extends ApiBaseController {

	private $showTransformer = null;
	private $playlistTransformer = null;

	public function __construct(ShowTransformer $showTransformer, PlaylistTransformer $playlistTransformer) {
		parent::__construct();
		$this->showTransformer = $showTransformer;
		$this->playlistTransformer = $playlistTransformer;
	}

	public function getService() {
		$data = [
			"apiVersion"			=> 1,
			"applicationVersion"	=> DebugHelpers::getVersion()
		];
		return $this->respond($data);
	}
	
	public function getShows() {
		$data = $this->showTransformer->transformCollection(Show::accessible()->orderBy("id")->get()->all());
		return $this->respond($data);
	}
	
	public function getShow($id) {
		$show = Show::with("playlists")->accessible()->find(intval($id));
		if (is_null($show)) {
			return $this->respondNotFound();
		}
		$data = array(
			"show"		=> $this->showTransformer->transform($show),
			"playlists"	=> $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all())
		);
		return $this->respond($data);
	}
	
	public function getPlaylists() {
		$data = $this->playlistTransformer->transformCollection(Playlist::accessibleToPublic()->orderBy("id")->get()->all());
		return $this->respond($data);
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
