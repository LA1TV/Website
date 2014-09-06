<?php namespace uk\co\la1tv\website\controllers\home\player;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use Response;
use Config;
use Facebook;
use Auth;

class PlayerController extends HomeBaseController {

	public function getIndex($playlistId, $mediaItemId) {
		
		// true if a user is logged into the cms and has permission to view media items.
		$userHasMediaItemsPermission = false;
		// true if a user is logged into the cms and has permission to view playlists.
		$userHasPlaylistsPermission = false;
		if (Auth::isLoggedIn()) {
			$userHasMediaItemsPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0);
			$userHasPlaylistsPermission = Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0);
		}
		
		$playlist = Playlist::with("show", "mediaItems")->accessible();
		if (!$userHasPlaylistsPermission) {
			// current cms user (if logged in) does not have permission to view playlists, so only search playlists accessible to the public.
			$playlist = $playlist->accessibleToPublic();
		}
		$playlist = $playlist->find(intval($playlistId));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$currentMediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		if (is_null($currentMediaItem)) {
			App::abort(404);
		}
		
		$playlistMediaItems = $playlist->mediaItems();
		if (!$userHasMediaItemsPermission) {
			$playlistMediaItems = $playlistMediaItems->accessible();
		}
		$playlistMediaItems = $playlistMediaItems->orderBy("media_item_to_playlist.position")->get();
		
		$playlistTableData = array();
		$activeItemIndex = null;
		foreach($playlistMediaItems as $i=>$item) {
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, 1920, 1080);
			$active = intval($item->id) === intval($currentMediaItem->id);
			if ($active) {
				$activeItemIndex = $i;
			}
			$accessible = $item->getIsAccessible();
			$title = $item->name;
			if (!$accessible) {
				$title = "[Inaccessible] ".$title;
			}
			$playlistTableData[] = array(
				"uri"			=> Config::get("custom.player_base_uri")."/".$playlist->id."/".$item->id,
				"active"		=> $active,
				"title"			=> $title,
				"episodeNo"		=> intval($item->pivot->position) + 1,
				"thumbnailUri"	=> $thumbnailUri,
				"accessible"	=> $accessible
			);
		}
		
		$playlistPreviousItemUri = null;
		$playlistNextItemUri = null;
		if ($activeItemIndex > 0) {
			$playlistPreviousItemUri = $playlistTableData[$activeItemIndex-1]['uri'];
		}
		if ($activeItemIndex < count($playlistTableData)-1) {
			$playlistNextItemUri = $playlistTableData[$activeItemIndex+1]['uri'];
		}

		$view = View::make("home.player.index");
		$view->episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$view->episodeDescription = $currentMediaItem->description;
		$view->episodeAccessible = $currentMediaItem->getIsAccessible();
		$view->playlistTitle = $playlist->generateName();
		$view->playlistTableData = $playlistTableData;
		$view->playlistNextItemUri = $playlistNextItemUri;
		$view->playlistPreviousItemUri = $playlistPreviousItemUri;
		$view->playerInfoUri = $this->getInfoUri($playlist->id, $currentMediaItem->id);
		$view->registerViewCountUri = $this->getRegisterViewCountUri($playlist->id, $currentMediaItem->id);
		$view->registerLikeUri = $this->getRegisterLikeUri($playlist->id, $currentMediaItem->id);
		$this->setContent($view, "player", "player");
	}
	
	// should return ajax response with information for the player.
	public function postPlayerInfo($playlistId, $mediaItemId) {
	
		// true if a user is logged into the cms and has permission to view media items.
		$userHasMediaItemsPermission = false;
		// true if a user is logged into the cms and has permission to view playlists.
		$userHasPlaylistsPermission = false;
		
		if (Auth::isLoggedIn()) {
			$userHasMediaItemsPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0);
			$userHasPlaylistsPermission = Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0);
		}
		
		$playlist = Playlist::accessible();
		if (!$userHasPlaylistsPermission) {
			// current cms user (if logged in) does not have permission to view playlists, so only search playlists accessible to the public.
			$playlist = $playlist->accessibleToPublic();
		}
		$playlist = $playlist->find(intval($playlistId));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "liveStreamItem.liveStream", "videoItem");
		
		$liveStreamItem = $mediaItem->liveStreamItem;
		if (!is_null($liveStreamItem) && !$liveStreamItem->getIsAccessible()) {
			// should not be accessible so pretend doesn't exist
			$liveStreamItem = null;
		}
		
		$hasLiveStreamItem = !is_null($liveStreamItem);
		$liveStream = $hasLiveStreamItem ? $liveStreamItem->liveStream : null;
		$videoItem = $mediaItem->videoItem;
		if (!is_null($videoItem) && !$videoItem->getIsAccessible()) {
			// should not be accessible so pretend doesn't exist
			$videoItem = null;
		}
		$hasVideoItem = !is_null($videoItem);
		
		$publishTime = $mediaItem->scheduled_publish_time;
		if (!is_null($publishTime)) {
			$publishTime = $publishTime->timestamp;
		}
		$coverArtUri = $playlist->getMediaItemCoverArtUri($mediaItem, 1920, 1080);
		$hasStream = $hasLiveStreamItem;
		$streamInfoMsg = $hasLiveStreamItem ? $liveStreamItem->information_msg : null;
		$streamState = $hasLiveStreamItem ? intval($liveStreamItem->getResolvedStateDefinition()->id): null;
		$availableOnDemand = $hasLiveStreamItem ? (boolean) $liveStreamItem->being_recorded : null;
		$streamViewCount = $hasLiveStreamItem ? intval($liveStreamItem->view_count) : null;
		$hasVod = $hasVideoItem;
		$vodLive = $hasVideoItem ? $videoItem->getIsLive() : null;
		$vodViewCount = $hasVideoItem ? intval($videoItem->view_count) : null;
		$numLikes = $mediaItem->likes()->count();
		$likeType = null;
		$user = Facebook::getUser();
		if (!is_null($user)) {
			$like = $mediaItem->likes()->where("site_user_id", $user->id)->first();
			if (!is_null($like)) {
				$likeType = $like->is_like ? "like" : "dislike";
			}
		}
		
		// only return the uris if they are actually needed. Security through obscurity
		// always return uris if there's a cms user with permission logged in because they should be able override the fact that it's not live
		
		$streamUris = array();
		// return the uris if the live stream is enabled (live), or the logged in cms user has permission
		// note $liveStream is the LiveStream model which is attached to the $liveStreamItem which is a MediaItemLiveStream model.
		if ($hasLiveStreamItem && ($streamState === 2 || $userHasMediaItemsPermission)) {
			foreach($liveStream->getUrisWithQualities() as $uriWithQuality) {
				$streamUris[] = array(
					"quality"	=> array(
						"id"	=> intval($uriWithQuality['qualityDefinition']->id),
						"name"	=> $uriWithQuality['qualityDefinition']->name
					),
					"uri"		=> $uriWithQuality['uri']
				);
			}
		}
		
		$videoUris = array();
		// return the uris if the item is accessible to the public or the logged in cms user has permission
		if ($hasVideoItem && ($vodLive || $userHasMediaItemsPermission)) {
			foreach($videoItem->getUrisWithQualities() as $uriWithQuality) {
				$videoUris[] = array(
					"quality"	=> array(
						"id"	=> intval($uriWithQuality['qualityDefinition']->id),
						"name"	=> $uriWithQuality['qualityDefinition']->name
					),
					"uris"		=> array($uriWithQuality['uri']) // this is an array because the front end player supports several different formats for one quality for different browsers. This allows for this if necessary in the future.
				);
			}
		}
		
		$data = array(
			"scheduledPublishTime"	=> $publishTime,
			"coverUri"				=> $coverArtUri,
			"hasStream"				=> $hasStream, // true if this media item has a live stream
			"streamInfoMsg"			=> $streamInfoMsg,
			"streamState"			=> $streamState, // 0=pending live, 1=live, 2=stream over, null=no stream
			"streamUris"			=> $streamUris,
			"availableOnDemand"		=> $availableOnDemand, // true if the stream is being recorded
			"streamViewCount"		=> $streamViewCount,
			"hasVod"				=> $hasVod, // true if this media item has a video.
			"vodLive"				=> $vodLive, // true when the video should be live to the public
			"videoUris"				=> $videoUris,
			"vodViewCount"			=> $vodViewCount,
			"numLikes"				=> $numLikes, // number of likes this media item has
			"likeType"				=> $likeType // "like" if liked, "dislike" if disliked, or null otherwise
		);
		
		return Response::json($data);
	}
	
	public function postRegisterView($playlistId, $mediaItemId) {
		$playlist = Playlist::accessibleToPublic()->find($playlistId);
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$success = false;
		if (isset($_POST['type'])) {
			$type = $_POST['type'];
			if ($type === "live" || $type === "vod") {
				if ($type === "live") {
					$liveStreamItem = $mediaItem->liveStreamItem;
					if (!is_null($liveStreamItem) && $liveStreamItem->getIsAccessible()) {
						$liveStreamItem->registerViewCount();
						$success = true;
					}
				}
				else {
					$videoItem = $mediaItem->videoItem;
					if (!is_null($videoItem) && $videoItem->getIsAccessible()) {
						$videoItem->registerViewCount();
						$success = true;
					}
				}
			}
		}
		return Response::json(array("success"=>$success));
	}
	
	public function postRegisterLike($playlistId, $mediaItemId) {
		$playlist = Playlist::accessibleToPublic()->find($playlistId);
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$success = false;
		if (isset($_POST['type'])) {
			$type = $_POST['type'];
			if ($type === "like" || $type === "dislike" || $type === "reset") {
				// an item can only be liked when it has an accessible video, or live stream which is enabled and not in the 'not live' state
				$mediaItemVideo = $mediaItem->videoItem;
				$mediaItemLiveStream = $mediaItem->liveStreamItem;
				
				$mediaItemVideoAccessible = !is_null($mediaItemVideo) && $mediaItemVideo->getIsAccessible();
				$mediaItemLiveStreamValidState = !is_null($mediaItemLiveStream) && $mediaItemLiveStream->getIsAccessible() && intval($mediaItemLiveStream->getResolvedStateDefinition()->id) !== 1;
				
				if ($mediaItemVideoAccessible || $mediaItemLiveStreamValidState) {
					$user = Facebook::getUser();
					if (!is_null($user)) {
						if ($type === "like") {
							$mediaItem->registerLike($user);
						}
						else if ($type === "dislike") {
							$mediaItem->registerDislike($user);
						}
						else if ($type === "reset") {
							$mediaItem->removeLike($user);
						}
						$success = true;
					}
				}
			}
		}
		return Response::json(array("success"=>$success));
	}
	
	private function getInfoUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_info_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getRegisterViewCountUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_register_view_count_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getRegisterLikeUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_register_like_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && ctype_digit($parameters[0])) {
			return call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			return parent::missingMethod($parameters);
		}
	}
}
