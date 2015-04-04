<?php namespace uk\co\la1tv\website\api;

use uk\co\la1tv\website\api\transformers\ShowTransformer;
use uk\co\la1tv\website\api\transformers\PlaylistTransformer;
use uk\co\la1tv\website\api\transformers\MediaItemTransformer;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use App;
use DebugHelpers;

class ApiResponseDataGenerator {
	
	private $showTransformer = null;
	private $playlistTransformer = null;
	private $mediaItemTransformer = null;
	
	public function __construct() {
		$this->showTransformer = new ShowTransformer();
		$this->playlistTransformer = new PlaylistTransformer();
		$this->mediaItemTransformer = new MediaItemTransformer();
	}

	
	public function generateServiceResponseData() {
		$data = [
			"applicationVersion"	=> DebugHelpers::getVersion()
		];
		return new ApiResponseData($data);
	}
	
	public function generatePermissionsResponseData($hasVodUrisPermission, $hasStreamUrisPermission) {
		$data = [
			"vodUris"		=> $hasVodUrisPermission,
			"streamUris"	=> $hasStreamUrisPermission
		];
		return new ApiResponseData($data);
	}
	
	public function generateShowsResponseData() {
		$data = $this->showTransformer->transformCollection(Show::accessible()->orderBy("id")->get()->all());
		return new ApiResponseData($data);
	}
	
	public function generateShowResponseData($id) {
		$show = Show::with("playlists")->accessible()->find(intval($id));
		if (is_null($show)) {
			return $this->generateNotFound();
		}
		$data = [
			"show"		=> $this->showTransformer->transform($show, []),
			"playlists"	=> $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all())
		];
		return new ApiResponseData($data);
	}
	
	public function generateShowPlaylistsResponseData($id) {
		$show = Show::with("playlists")->accessible()->find(intval($id));
		if (is_null($show)) {
			return $this->generateNotFound();
		}
		$data = $this->playlistTransformer->transformCollection($show->playlists()->accessibleToPublic()->orderBy("id")->get()->all());
		return new ApiResponseData($data);
	}
	
	public function generatePlaylistsResponseData() {
		$data = $this->playlistTransformer->transformCollection(Playlist::accessibleToPublic()->orderBy("id")->get()->all());
		return new ApiResponseData($data);
	}
	
	public function generatePlaylistResponseData($id, $showStreamUris, $showVodUris) {
		$playlist = Playlist::accessible()->find(intval($id));
		if (is_null($playlist)) {
			return $this->generateNotFound();
		}
		$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem");
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
		$data = [
			"playlist"		=> $this->playlistTransformer->transform($playlist, []),
			"mediaItems"	=> $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems), $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris))
		];
		return new ApiResponseData($data);
	}
	
	public function generatePlaylistMediaItemsResponseData($id, $showStreamUris, $showVodUris) {
		$playlist = Playlist::accessible()->find(intval($id));
		if (is_null($playlist)) {
			return $this->generateNotFound();
		}
		$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem");
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
		$data = $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems), $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris));
		return new ApiResponseData($data);
	}
	
	public function generateMediaItemResponseData($playlistId, $mediaItemId, $showStreamUris, $showVodUris) {
		$playlist = Playlist::accessible()->find(intval($playlistId));
		if (is_null($playlist)) {
			return $this->generateNotFound();
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find(intval($mediaItemId));
		if (is_null($mediaItem)) {
			return $this->generateNotFound();
		}
		$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem");
		$data = $this->mediaItemTransformer->transform([$playlist, $mediaItem], $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris));
		return new ApiResponseData($data);
	}
	
	
	private function generateNotFound() {
		return new ApiResponseData([], 404); 
	}
	
	private function createMediaItemsWithPlaylists($playlist, $mediaItems) {
		$mediaItemsWithPlaylists = [];
		foreach($mediaItems as $mediaItem) {
			$mediaItemsWithPlaylists[] = [$playlist, $mediaItem];
		}
		return $mediaItemsWithPlaylists;
	}
	
	private function getMediaItemTransformerOptions($showStreamUris, $showVodUris) {
		return [
			"showStreamUris"	=> $showStreamUris,
			"showVodUris"		=> $showVodUris
		];
	}
}