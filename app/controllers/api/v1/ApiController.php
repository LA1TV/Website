<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use uk\co\la1tv\website\transformers\ShowTransformer;
use uk\co\la1tv\website\transformers\PlaylistTransformer;
use uk\co\la1tv\website\transformers\MediaItemTransformer;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use DebugHelpers;

class ApiController extends ApiBaseController {

	private $showTransformer = null;
	private $playlistTransformer = null;
	private $mediaItemTransformer = null;

	public function __construct(ShowTransformer $showTransformer, PlaylistTransformer $playlistTransformer, MediaItemTransformer $mediaItemTransformer) {
		parent::__construct();
		$this->showTransformer = $showTransformer;
		$this->playlistTransformer = $playlistTransformer;
		$this->mediaItemTransformer = $mediaItemTransformer;
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
		$data = [
			"show"		=> $this->showTransformer->transform($show),
			"playlists"	=> $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all())
		];
		return $this->respond($data);
	}
	
	public function getShowPlaylists($id) {
		$show = Show::with("playlists")->accessible()->find(intval($id));
		if (is_null($show)) {
			return $this->respondNotFound();
		}
		$data = $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all());
		return $this->respond($data);
	}
	
	public function getPlaylists() {
		$data = $this->playlistTransformer->transformCollection(Playlist::accessibleToPublic()->orderBy("id")->get()->all());
		return $this->respond($data);
	}
	
	public function getPlaylist($id) {
		$playlist = Playlist::with("mediaItems")->accessible()->find(intval($id));
		if (is_null($playlist)) {
			return $this->respondNotFound();
		}
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
		$data = [
			"playlist"		=> $this->playlistTransformer->transform($playlist),
			"mediaItems"	=> $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems))
		];
		return $this->respond($data);
	}
	
	public function getPlaylistMediaItems($id) {
		$playlist = Playlist::with("mediaItems")->accessible()->find(intval($id));
		if (is_null($playlist)) {
			return $this->respondNotFound();
		}
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
		$data = $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems));
		return $this->respond($data);
	}
	
	public function getMediaItem($playlistId, $mediaItemId) {
		$playlist = Playlist::with("mediaItems")->accessible()->find(intval($playlistId));
		if (is_null($playlist)) {
			return $this->respondNotFound();
		}
		$mediaItem = $playlist->mediaItems()->accessible()->find(intval($mediaItemId));
		if (is_null($mediaItem)) {
			return $this->respondNotFound();
		}
		$data = $this->mediaItemTransformer->transform([$playlist, $mediaItem]);
		return $this->respond($data);
	}
	
	private function createMediaItemsWithPlaylists($playlist, $mediaItems) {
		$mediaItemsWithPlaylists = [];
		foreach($mediaItems as $mediaItem) {
			$mediaItemsWithPlaylists[] = [$playlist, $mediaItem];
		}
		return $mediaItemsWithPlaylists;
	}
}
