<?php namespace uk\co\la1tv\website\api;

use uk\co\la1tv\website\api\transformers\ShowTransformer;
use uk\co\la1tv\website\api\transformers\PlaylistTransformer;
use uk\co\la1tv\website\api\transformers\MediaItemTransformer;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use App;
use DebugHelpers;
use Exception;
use Config;

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
			"applicationVersion"	=> DebugHelpers::getVersion(),
			"degradedService"		=> Config::get("degradedService.enabled")
		];
		return new ApiResponseData($data);
	}
	
	public function generatePermissionsResponseData($hasVodUrisPermission, $hasStreamUrisPermission, $hasWebhooksPermission) {
		$data = [
			"vodUris"		=> $hasVodUrisPermission,
			"streamUris"	=> $hasStreamUrisPermission,
			"webhooks"		=> $hasWebhooksPermission
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
		$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem", "mediaItems.videoItem.sourceFile.vodData");
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
		$playlist->load("mediaItems.liveStreamItem", "mediaItems.liveStreamItem.stateDefinition", "mediaItems.liveStreamItem.liveStream", "mediaItems.videoItem", "mediaItems.videoItem.sourceFile.vodData");
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get()->all();
		$data = $this->mediaItemTransformer->transformCollection($this->createMediaItemsWithPlaylists($playlist, $mediaItems), $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris));
		return new ApiResponseData($data);
	}
	
	public function generatePlaylistMediaItemResponseData($playlistId, $mediaItemId, $showStreamUris, $showVodUris) {
		$playlist = Playlist::accessible()->find(intval($playlistId));
		if (is_null($playlist)) {
			return $this->generateNotFound();
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find(intval($mediaItemId));
		if (is_null($mediaItem)) {
			return $this->generateNotFound();
		}
		$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem", "videoItem.sourceFile.vodData");
		$data = $this->mediaItemTransformer->transform([$playlist, $mediaItem], $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris));
		return new ApiResponseData($data);
	}
	
	// $limit is the maximum amount of items to be retrieved
	// $sortMode can be "POPULARITY", "SCHEDULED_PUBLISH_TIME"
	// $sortDirection can be "ASC" or "DESC". Only "DESC" supported for "VIEW_COUNT"
	// $vodIncludeSetting can be "VOD_OPTIONAL", "HAS_VOD", "HAS_AVAILABLE_VOD", "VOD_PROCESSING"
	// $streamIncludeSetting can be "STREAM_OPTIONAL", "HAS_STREAM", "HAS_LIVE_STREAM"
	// the $vodIncludeSetting and $streamIncludeSetting are or'd together. E.g if HAS_VOD and HAS_LIVE_STREAM then
	// all items will have either vod, or a stream that's live, or both
	public function generateMediaItemsResponseData($limit, $sortMode, $sortDirection, $vodIncludeSetting, $streamIncludeSetting, $showStreamUris, $showVodUris) {
		$maxLimit = Config::get("api.mediaItemsMaxRetrieveLimit");
		if ($limit > $maxLimit) {
			$limit = $maxLimit;
		}
		
		$mediaItems = null;
		if ($sortMode === "POPULARITY") {
			if ($sortDirection === "ASC") {
				throw(new Exception("ASC sort direction not supported for POPULARITY sort mode."));
			}
			else if ($sortDirection === "DESC") {
				// intentional
			}
			else {
				throw(new Exception("Invalid sort direction."));
			}
			
			$items = MediaItem::getCachedMostPopularItems();
			$allMediaItems = array_column($items, 'mediaItem');
			$mediaItems = [];
			foreach($allMediaItems as $a) {
				$includeVod = null;
				if ($vodIncludeSetting === "VOD_OPTIONAL") {
					// intentional
				}
				else if ($vodIncludeSetting === "HAS_VOD") {
					$includeVod = !is_null($a->videoItem) && $a->videoItem->getIsAccessible();
				}
				else if ($vodIncludeSetting === "HAS_AVAILABLE_VOD") {
					$includeVod = !is_null($a->videoItem) && $a->getIsAccessible() && $a->videoItem->getIsLive();
				}
				else if ($vodIncludeSetting === "VOD_PROCESSING") {
					throw(new Exception("VOD_PROCESSING cannot be used with POPULARITY sort mode."));	
				}
				else {
					throw(new Exception("Invalid vod include setting."));
				}
				
				$includeStream = null;
				if ($streamIncludeSetting === "STREAM_OPTIONAL") {
					// intentional
				}
				else if ($streamIncludeSetting === "HAS_STREAM") {
					$includeStream = !is_null($a->liveStreamItem) && $a->liveStreamItem->getIsAccessible();
				}
				else if ($streamIncludeSetting === "HAS_LIVE_STREAM") {
					$includeStream = !is_null($a->liveStreamItem) && $a->liveStreamItem->getIsAccessible() && $a->liveStreamItem->isLive();
				}
				else {
					throw(new Exception("Invalid stream include setting."));
				}
				
				if ((is_null($includeStream) && is_null($includeVod)) || $includeStream || $includeVod) {
					$mediaItems[] = $a;
				}
			}
		}
		else if ($sortMode === "SCHEDULED_PUBLISH_TIME") {
			$mediaItems = MediaItem::with("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem", "videoItem.sourceFile.vodData")->accessible();
			$mediaItems = $mediaItems->where(function($q) use (&$vodIncludeSetting, &$streamIncludeSetting) {
				if ($vodIncludeSetting === "VOD_OPTIONAL") {
					// intentional
				}
				else if ($vodIncludeSetting === "HAS_VOD") {
					$q->whereHas("videoItem", function($q2) {
						$q2->accessible();
					});
				}
				else if ($vodIncludeSetting === "HAS_AVAILABLE_VOD") {
					$q->whereHas("videoItem", function($q2) {
						$q2->live();
					});
				}
				else if ($vodIncludeSetting === "VOD_PROCESSING") {
					$q->whereHas("videoItem", function($q2) {
						$q2->whereHas("sourceFile", function($q3) {
							$q3->finishedProcessing(false);
						});
					});
				}
				else {
					throw(new Exception("Invalid vod include setting."));
				}
				
				if ($streamIncludeSetting === "STREAM_OPTIONAL") {
					// intentional
				}
				else if ($streamIncludeSetting === "HAS_STREAM") {
					$q->orWhereHas("liveStreamItem", function($q2) {
						$q2->accessible();
					});
				}
				else if ($streamIncludeSetting === "HAS_LIVE_STREAM") {
					$q->orWhereHas("liveStreamItem", function($q2) {
						$q2->live();
					});
				}
				else {
					throw(new Exception("Invalid stream include setting."));
				}
			});
			
			$sortAsc = null;
			if ($sortDirection === "ASC") {
				$sortAsc = true;
			}
			else if ($sortDirection === "DESC") {
				$sortAsc = false;
			}
			else {
				throw(new Exception("Invalid sort direction."));
			}
			$mediaItems = $mediaItems->orderBy("media_items.scheduled_publish_time", $sortAsc ? "asc" : "desc")->orderBy("id", "asc")->take($limit)->get()->all();
		}
		else {
			throw(new Exception("Invalid sort mode."));
		}
		
		$mediaItemsAndPlaylists = [];
		foreach($mediaItems as $a) {
			$mediaItemsAndPlaylists[] = [null, $a];
		}
		
		$data = [
			"mediaItems"	=> $this->mediaItemTransformer->transformCollection($mediaItemsAndPlaylists, $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris))
		];
		return new ApiResponseData($data);
	}
	
	public function generateMediaItemResponseData($mediaItemId, $showStreamUris, $showVodUris) {
		$mediaItem = MediaItem::accessible()->find(intval($mediaItemId));
		if (is_null($mediaItem)) {
			return $this->generateNotFound();
		}
		$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem", "videoItem.sourceFile.vodData");
		
		$playlists = $mediaItem->playlists()->orderBy("id", "asc")->get()->all();
		
		$data = [
			"mediaItem"	=> $this->mediaItemTransformer->transform([null, $mediaItem], $this->getMediaItemTransformerOptions($showStreamUris, $showVodUris)),
			"playlists"	=> $this->playlistTransformer->transformCollection($playlists)
		];
		return new ApiResponseData($data);
	}
	
	public function generateMediaItemPlaylistsResponseData($mediaItemId) {
		$mediaItem = MediaItem::accessible()->find(intval($mediaItemId));
		if (is_null($mediaItem)) {
			return $this->generateNotFound();
		}
		$playlists = $mediaItem->playlists()->orderBy("id", "asc")->get()->all();
		$data = $this->playlistTransformer->transformCollection($playlists);
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