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
		return $this->withCache("shows", 15, function() {
			$data = $this->showTransformer->transformCollection(Show::accessible()->orderBy("id")->get()->all());
			return $this->respond($data);
		});
	}
	
	public function getShow($id) {
		return $this->withCache("show-".$id, 15, function() use (&$id) {
			$show = Show::with("playlists")->accessible()->find(intval($id));
			if (is_null($show)) {
				return $this->respondNotFound();
			}
			$data = [
				"show"		=> $this->showTransformer->transform($show),
				"playlists"	=> $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all())
			];
			return $this->respond($data);
		});
	}
	
	public function getShowPlaylists($id) {
		return $this->withCache("show-playlist-".$id, 15, function() use (&$id) {
			$show = Show::with("playlists")->accessible()->find(intval($id));
			if (is_null($show)) {
				return $this->respondNotFound();
			}
			$data = $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all());
			return $this->respond($data);
		});
	}
	
	public function getPlaylists() {
		return $this->withCache("playlists", 15, function() {
			$data = $this->playlistTransformer->transformCollection(Playlist::accessibleToPublic()->orderBy("id")->get()->all());
			return $this->respond($data);
		});
	}
	
	public function getPlaylist($id) {
		return $this->withCache("playlist-".$id, 8, function() use (&$id) {
			$playlist = Playlist::accessible()->find(intval($id));
			if (is_null($playlist)) {
				return $this->respondNotFound();
			}
			$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem");
			$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
			$data = [
				"playlist"		=> $this->playlistTransformer->transform($playlist),
				"mediaItems"	=> $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems))
			];
			return $this->respond($data);
		});
	}
	
	public function getPlaylistMediaItems($id) {
		return $this->withCache("playlist-media-items-".$id, 8, function() use (&$id) {
			$playlist = Playlist::accessible()->find(intval($id));
			if (is_null($playlist)) {
				return $this->respondNotFound();
			}
			$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem");
			$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
			$data = $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems));
			return $this->respond($data);
		});
	}
	
	public function getMediaItem($playlistId, $mediaItemId) {
		return $this->withCache("media-item-".$playlistId."-".$mediaItemId, 4, function() use (&$playlistId, &$mediaItemId) {
			$playlist = Playlist::accessible()->find(intval($playlistId));
			if (is_null($playlist)) {
				return $this->respondNotFound();
			}
			
			$mediaItem = $playlist->mediaItems()->accessible()->find(intval($mediaItemId));
			if (is_null($mediaItem)) {
				return $this->respondNotFound();
			}
			$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem");
			$data = $this->mediaItemTransformer->transform([$playlist, $mediaItem]);
			return $this->respond($data);
		});
	}
	
	private function createMediaItemsWithPlaylists($playlist, $mediaItems) {
		$mediaItemsWithPlaylists = [];
		foreach($mediaItems as $mediaItem) {
			$mediaItemsWithPlaylists[] = [$playlist, $mediaItem];
		}
		return $mediaItemsWithPlaylists;
	}
}
