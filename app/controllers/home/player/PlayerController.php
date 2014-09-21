<?php namespace uk\co\la1tv\website\controllers\home\player;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\LiveStreamStateDefinition;
use Response;
use Config;
use Carbon;
use Facebook;
use Auth;
use FormHelpers;
use URLHelpers;
use Exception;

class PlayerController extends HomeBaseController {

	public function getIndex($playlistId, $mediaItemId) {
		
		// true if a user is logged into the cms and has permission to view media items.
		$userHasMediaItemsPermission = false;
		// true if a user is logged into the cms and has permission to edit media items.
		$userHasMediaItemsEditPermission = false;
		// true if a user is logged into the cms and has permission to view playlists.
		$userHasPlaylistsPermission = false;
		// true if a user is logged into the cms and has permission to manage comments and post as station.
		$userHasCommentsPermission = false;
		if (Auth::isLoggedIn()) {
			$userHasMediaItemsPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0);
			$userHasMediaItemsEditPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 1);
			$userHasPlaylistsPermission = Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0);
			$userHasCommentsPermission = Auth::getUser()->hasPermission(Config::get("permissions.comments"), 0);
		}
		
		$playlist = Playlist::with("show", "mediaItems", "relatedItems", "relatedItems.playlists")->accessible();
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
		
		$playlistMediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get();
		$playlistTableData = array();
		$activeItemIndex = null;
		foreach($playlistMediaItems as $i=>$item) {
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, 1920, 1080);
			$active = intval($item->id) === intval($currentMediaItem->id);
			if ($active) {
				$activeItemIndex = $i;
			}
			$playlistName = null;
			if (is_null($playlist->show)) {
				// this is a playlist not a series.
				// show the series/playlist that each video in the playlist is from
				$defaultPlaylist = $item->getDefaultPlaylist();
				if (!is_null($defaultPlaylist->show)) {
					// the current item in the playlist is part of a show.
					$playlistName = $defaultPlaylist->generateName();
				}
			}
			$playlistTableData[] = array(
				"uri"					=> $playlist->getUri($item),
				"active"				=> $active,
				"title"					=> $item->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $playlistName,
				"episodeNo"				=> intval($item->pivot->position) + 1,
				"thumbnailUri"			=> $thumbnailUri
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
		
		$relatedItems = $playlist->generateRelatedItems($currentMediaItem);
		$relatedItemsTableData = array();
		foreach($relatedItems as $i=>$item) {
			// a mediaitem can be part of several playlists. Always use the first one that has a show if there is one, or just the first one otherwise
			$relatedItemPlaylist = $item->getDefaultPlaylist();
			$thumbnailUri = $relatedItemPlaylist->getMediaItemCoverArtUri($item, 1920, 1080);
			$relatedItemsTableData[] = array(
				"uri"					=> $relatedItemPlaylist->getUri($item),
				"active"				=> false,
				"title"					=> $item->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $relatedItemPlaylist->generateName(),
				"episodeNo"				=> $i+1,
				"thumbnailUri"			=> $thumbnailUri
			);
		}
		
		$streamControlData = null;
		$currentMediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition");
		$liveStreamItem = $currentMediaItem->liveStreamItem;
		if ($userHasMediaItemsEditPermission && !is_null($liveStreamItem)) {
			$infoMsg = $liveStreamItem->information_msg;
			$liveStreamStateDefinitions = LiveStreamStateDefinition::orderBy("id", "asc")->get();
			$streamStateButtonsData = array();
			foreach($liveStreamStateDefinitions as $a) {
				$streamStateButtonsData[] = array(
					"id"	=> intval($a->id),
					"text"	=> $a->name
				);
			}
			$liveStream = $liveStreamItem->liveStream;
			$streamControlData = array(
				"showInaccessibleWarning"	=> !$liveStreamItem->getIsAccessible(),
				"showNoLiveStreamWarning"	=> is_null($liveStream),
				"showLiveStreamNotAccessibleWarning"	=> !is_null($liveStream) && !$liveStream->getIsAccessible(),
				"showStreamReadyForLiveMsg"	=> !is_null($liveStream) && $liveStream->getIsAccessible(),
				"streamStateButtonsData"	=> $streamStateButtonsData,
				"streamStateChosenId"		=> $liveStreamItem->stateDefinition->id,
				"streamInfoMsg"				=> !is_null($infoMsg) ? $infoMsg : ""
			);
		}
		
		$seriesAd = null;
		if (is_null($playlist->show)) {
			// user is currently browsing playlist not series
			$defaultPlaylist = $currentMediaItem->getDefaultPlaylist();
			if (!is_null($defaultPlaylist->show)) {
				// show the button to link the user to the series containing the video they are watching.
				$seriesAd = array(
					"name"	=> $defaultPlaylist->generateName(),
					"uri"		=> $defaultPlaylist->getUri($currentMediaItem)
				);
			}
		}

		$view = View::make("home.player.index");
		$view->episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$view->episodeDescriptionEscaped = !is_null($currentMediaItem->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($currentMediaItem->description)) : null;
		$view->episodeAccessibleToPublic = true; // TODO;	
		$view->playlistTableFragment = View::make("fragments.home.playlist", array(
			"headerRowData"	=> array(
				"title" 			=> $playlist->generateName(),
				"navButtons"		=> array(
					"previousItemUri"	=> $playlistPreviousItemUri,
					"nextItemUri"		=> $playlistNextItemUri
				)
			),
			"tableData"		=> $playlistTableData
		));
		$view->relatedItemsTableFragment = count($relatedItemsTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> array(
				"title" 		=> "Related Items",
				"navButtons"	=> null
			),
			"tableData"		=> $relatedItemsTableData
		)) : null;
		
		
		$view->playerInfoUri = $this->getInfoUri($playlist->id, $currentMediaItem->id);
		$view->registerViewCountUri = $this->getRegisterViewCountUri($playlist->id, $currentMediaItem->id);
		$view->registerLikeUri = $this->getRegisterLikeUri($playlist->id, $currentMediaItem->id);
		$view->adminOverrideEnabled = $userHasMediaItemsPermission;
		$view->loginRequiredMsg = "Please log in to use this feature.";
		$view->getCommentsUri = $this->getGetCommentsUri($currentMediaItem->id);
		$view->postCommentUri = $this->getPostCommentUri($currentMediaItem->id);
		$view->deleteCommentUri = $this->getDeleteCommentUri($currentMediaItem->id);
		$view->canCommentAsFacebookUser = Facebook::isLoggedIn();
		$view->canCommentAsStation = $userHasCommentsPermission;
		$view->streamControlData = $streamControlData;
		$view->mediaItemId = $currentMediaItem->id;
		$view->seriesAd = $seriesAd;
		$view->coverImageUri = $playlist->getMediaItemCoverUri($currentMediaItem, 940, 150);
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
		$embedData = $playlist->getEmbedData($mediaItem);
		
		// only return the uris if they are actually needed. Security through obscurity
		// always return uris if there's a cms user with permission logged in because they should be able override the fact that it's not live
		
		$streamUris = array();
		// return the uris if the live stream is enabled (live), or the logged in cms user has permission
		// note $liveStream is the LiveStream model which is attached to the $liveStreamItem which is a MediaItemLiveStream model.
		if ($hasLiveStreamItem && !is_null($liveStream) && $liveStream->getIsAccessible() && ($streamState === 2 || $userHasMediaItemsPermission)) {
			foreach($liveStream->getQualitiesWithUris() as $qualityWithUris) {
				$streamUris[] = array(
					"quality"	=> array(
						"id"	=> intval($qualityWithUris['qualityDefinition']->id),
						"name"	=> $qualityWithUris['qualityDefinition']->name
					),
					"uris"		=> $qualityWithUris['uris']
				);
			}
		}
		
		$videoUris = array();
		// return the uris if the item is accessible to the public or the logged in cms user has permission
		if ($hasVideoItem && ($vodLive || $userHasMediaItemsPermission)) {
			foreach($videoItem->getQualitiesWithUris() as $qualityWithUris) {
				$videoUris[] = array(
					"quality"	=> array(
						"id"	=> intval($qualityWithUris['qualityDefinition']->id),
						"name"	=> $qualityWithUris['qualityDefinition']->name
					),
					"uris"		=> $qualityWithUris['uris']
				);
			}
		}
		
		$data = array(
			"scheduledPublishTime"	=> $publishTime,
			"coverUri"				=> $coverArtUri,
			"embedData"				=> $embedData,
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
				
				$mediaItemVideoAccessible = !is_null($mediaItemVideo) && $mediaItemVideo->getIsLive();
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
	
	public function postComments($mediaItemId) {
		
		$mediaItem = MediaItem::with("comments", "comments.siteUser")->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		// X = a number of comments
		// id = the id of the comment to start at. -1 means return the last X comments. loadLaterComments must be false in this case
		// load_later_comments = if true return all comments from the specified id otherwise load comments before it.
		// the id that is provided isn't checked to be valid as the comment it points too may have been deleted.
		
		$id = FormHelpers::getValue("id");
		$loadLaterComments = FormHelpers::getValue("load_later_comments") === "1";
		
		$id = !is_null($id) ? intval($id) : null;
		if (is_null($id)) {
			throw(new Exception("Id must be set."));
		}
		else if ($id === -1 && $loadLaterComments) {
			throw(new Exception("If the id is -1 then load_later_comments must be false."));
		}
		
		$commentsModels = null;
		$more = null;
		if ($loadLaterComments) {
			$commentsModels = $mediaItem->comments()->orderBy("id", "asc")->where("id", ">=", $id)->limit(Config::get("comments.number_to_retrieve")+1)->get();
			$more = $commentsModels->count() === Config::get("comments.number_to_retrieve")+1;
			if ($more) {
				$commentsModels->pop();
			}
		}
		else {
			$commentsModels = $mediaItem->comments()->orderBy("id", "desc");
			if ($id !== -1) {
				$commentsModels = $commentsModels->where("id", "<=", $id);
			}
			$commentsModels = $commentsModels->limit(Config::get("comments.number_to_retrieve")+1)->get();
			$more = $commentsModels->count() === Config::get("comments.number_to_retrieve")+1;
			if ($more) {
				$commentsModels->pop();
			}
			$commentsModels = $commentsModels->reverse(); // get in ascending order
		}
		
		// true if a user is logged into the cms and has permission to manage comments and post as station.
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.comments"), 0);
		
		$comments  = array();
		foreach($commentsModels as $a) {
			// should not be returning supplied id
			if ($id !== -1 && intval($a->id) === $id) {
				continue;
			}
			$siteUser = $a->siteUser;
			$permissionToDelete = $userHasCommentsPermission || (Facebook::isLoggedIn() && !is_null($siteUser) && intval(Facebook::getUser()->id) === intval($siteUser->id));
			$comments[] = array(
				"id"					=> intval($a->id),
				"profilePicUri"			=> !is_null($siteUser) ? $siteUser->getProfilePicUri(100, 100) : Config::get("comments.station_profile_picture_uri"),
				"postTime"				=> $a->created_at->timestamp,
				"name"					=> !is_null($siteUser) ? $siteUser->name : Config::get("comments.station_name"),
				"msg"					=> $a->msg,
				"permissionToDelete"	=> $permissionToDelete,
				"edited"				=> (boolean) $a->edited
			);
		}
		
		$response = array(
			"comments"	=> $comments, // the comments as array("id", "profilePicUri", "postTime", "name", "msg", "edited"), in order of the newest comments last
			"more"		=> $more // true if there are more comments in the direction that is being returned
		);
		return Response::json($response);
	}
	
	public function postPostComment($mediaItemId) {
		
		$mediaItem = MediaItem::accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		// true if a user is logged into the cms and has permission to manage comments and post as station.
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.comments"), 0);
		
		if ((!Facebook::isLoggedIn() || Facebook::getUserState() !== 0) && !$userHasCommentsPermission) {
			App::abort(403);
		}
		
		$response = array("success" => false);
		
		// check if user posted a comment recently
		$noRecentComments = MediaItemComment::where("site_user_id", $userHasCommentsPermission ? null : Facebook::getUser()->id)->where("updated_at", ">=", Carbon::now()->subSeconds(Config::get("comments.number_allowed_reset_interval")))->count();
		if ($noRecentComments <= Config::get("comments.number_allowed")) {
		
			$msg = FormHelpers::getValue("msg");
			$postAsStation = FormHelpers::getValue("post_as_station") === "1";
			if (is_null($msg)) {
				throw(new Exception("No message supplied."));
			}
			else if (strlen($msg) > 500) {
				throw(new Exception("Message length must be <= 500 characters."));
			}
			else if ($postAsStation && !$userHasCommentsPermission) {
				App::abort(403);
			}
			else if (!$postAsStation && !Facebook::isLoggedIn()) {
				throw(new Exception("Cannot post as a facebook user as not logged in as one."));
			}
			
			$msg = trim($msg); // remove leading and trailing whitespace.
			
			if ($msg === "") {
				throw(new Exception("The message cannot be blank."));
			}
			
			$comment = new MediaItemComment(array(
				"msg"	=> $msg
			));
			
			if (!$postAsStation) {
				$comment->siteUser()->associate(Facebook::getUser());
			}
			$comment->mediaItem()->associate($mediaItem);
			$comment->save();
			$response['success'] = true;
			$response['id'] = intval($comment->id);
		}
		return Response::json($response);
	}
	
	public function postDeleteComment($mediaItemId) {
		
		$mediaItem = MediaItem::accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		// true if a user is logged into the cms and has permission to manage comments and post as station.
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.comments"), 0);
		
		if ((!Facebook::isLoggedIn() || Facebook::getUserState() !== 0) && !$userHasCommentsPermission) {
			App::abort(403);
		}
		
		$id = FormHelpers::getValue("id");
		if (is_null($id)) {
			throw(new Exception("Id must be supplied."));
		}
		$id = intval($id);
		
		$comment = $mediaItem->comments()->find($id);
		if (is_null($comment)) {
			throw(new Exception("Comment could not be found."));
		}
		
		if (!$userHasCommentsPermission && intval($comment->siteUser->id) !== intval(Facebook::getUser()->id)) {
			App::abort(403);
		}
		
		$comment->delete();
		return Response::json(array("success"=>true));
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
	
	private function getGetCommentsUri($mediaItemId) {
		return Config::get("comments.get_base_uri")."/".$mediaItemId;
	}
	
	private function getPostCommentUri($mediaItemId) {
		return Config::get("comments.post_base_uri")."/".$mediaItemId;
	}
	
	private function getDeleteCommentUri($mediaItemId) {
		return Config::get("comments.delete_base_uri")."/".$mediaItemId;
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
