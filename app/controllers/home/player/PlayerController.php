<?php namespace uk\co\la1tv\website\controllers\home\player;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use DB;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\LiveStreamStateDefinition;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\PlaybackTime;
use Response;
use Config;
use Carbon;
use Facebook;
use Auth;
use FormHelpers;
use URLHelpers;
use Exception;
use PlaylistTableHelpers;

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
			$userHasCommentsPermission = Auth::getUser()->hasPermission(Config::get("permissions.siteComments"), 0);
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
		
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		// retrieving inaccessible items as well and then skipping them in the loop. This is so that we get the correct episode number.
		$playlistMediaItems = $playlist->mediaItems()->orderBy("media_item_to_playlist.position")->get();
		$playlistTableData = array();
		$activeItemIndex = null;
		
		$newIndex = 0;
		foreach($playlistMediaItems as $i=>$item) {
			if (!$item->getIsAccessible()) {
				// this shouldn't be accessible
				continue;
			}
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
			$active = intval($item->id) === intval($currentMediaItem->id);
			if ($active) {
				$activeItemIndex = $newIndex;
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
				"uri"					=> $playlist->getMediaItemUri($item),
				"active"				=> $active,
				"title"					=> $item->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $playlistName,
				"episodeNo"				=> $i+1,
				"thumbnailUri"			=> $thumbnailUri,
				"thumbnailFooter"		=> PlaylistTableHelpers::getFooterObj($item)
			);
			$newIndex++;
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
			$thumbnailUri = $relatedItemPlaylist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
			$relatedItemsTableData[] = array(
				"uri"					=> $relatedItemPlaylist->getMediaItemUri($item),
				"active"				=> false,
				"title"					=> $item->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $relatedItemPlaylist->generateName(),
				"episodeNo"				=> $i+1,
				"thumbnailUri"			=> $thumbnailUri,
				"thumbnailFooter"		=> PlaylistTableHelpers::getFooterObj($item)
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
				"showExternalStreamLocationMsg"	=> !is_null($liveStreamItem->external_stream_url),
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
					"uri"		=> $defaultPlaylist->getMediaItemUri($currentMediaItem)
				);
			}
		}
		
		$episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$openGraphCoverArtUri = $playlist->getMediaItemCoverArtUri($currentMediaItem, $coverArtResolutions['fbOpenGraph']['w'], $coverArtResolutions['fbOpenGraph']['h']);
		$twitterCardCoverArtUri = $playlist->getMediaItemCoverArtUri($currentMediaItem, $coverArtResolutions['twitterCard']['w'], $coverArtResolutions['twitterCard']['h']);
			
		$twitterProperties = array();
		$twitterProperties[] = array("name"=> "card", "content"=> "player");
		$openGraphProperties = array();
		if (is_null($playlist->show)) {
			$openGraphProperties[] = array("name"=> "og:type", "content"=> "video.other");
		}
		else {
			$openGraphProperties[] = array("name"=> "og:type", "content"=> "video.episode");
			$openGraphProperties[] = array("name"=> "video:series", "content"=> $playlist->getUri());
		}
		$twitterProperties[] = array("name"=> "player", "content"=> $playlist->getMediaItemEmbedUri($currentMediaItem)."?autoPlayVod=0&autoPlayStream=0&flush=1&disableFullScreen=1&disableRedirect=1");
		$twitterProperties[] = array("name"=> "player:width", "content"=> "1280");
		$twitterProperties[] = array("name"=> "player:height", "content"=> "720");
		
		
		if (!is_null($currentMediaItem->description)) {
			$openGraphProperties[] = array("name"=> "og:description", "content"=> $currentMediaItem->description);
			$twitterProperties[] = array("name"=> "description", "content"=> str_limit($currentMediaItem->description, 197, "..."));
		}
		$openGraphProperties[] = array("name"=> "video:release_date", "content"=> $currentMediaItem->scheduled_publish_time->toISO8601String());;
		$openGraphProperties[] = array("name"=> "og:title", "content"=> $episodeTitle);
		$twitterProperties[] = array("name"=> "title", "content"=> $episodeTitle);
		$openGraphProperties[] = array("name"=> "og:image", "content"=> $openGraphCoverArtUri);
		$twitterProperties[] = array("name"=> "image", "content"=> $twitterCardCoverArtUri);
		if (!is_null($playlist->show)) {
			if (!is_null($playlistNextItemUri)) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $playlistNextItemUri);
			}
			if (!is_null($playlistPreviousItemUri)) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $playlistPreviousItemUri);
			}
		}
		foreach($relatedItemsTableData as $a) {
			if (!in_array($a['uri'], array($playlistNextItemUri, $playlistPreviousItemUri))) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
			}
		}
		
		$view = View::make("home.player.index");
		$view->episodeTitle = $episodeTitle;
		$view->episodeDescriptionEscaped = !is_null($currentMediaItem->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($currentMediaItem->description)) : null;
		$view->playlistTableFragment = View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> array(
				"title" 			=> $playlist->generateName(),
				"seriesUri"			=> !is_null($playlist->show) ? $playlist->show->getUri() : null,
				"navButtons"		=> array(
					"previousItemUri"		=> $playlistPreviousItemUri,
					"nextItemUri"			=> $playlistNextItemUri,
					"showAutoPlayButton"	=> true
				)
			),
			"tableData"		=> $playlistTableData
		));
		$view->relatedItemsTableFragment = count($relatedItemsTableData) > 0 ? View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> array(
				"title" 		=> "Related Items",
				"seriesUri"		=> null,
				"navButtons"	=> null
			),
			"tableData"		=> $relatedItemsTableData
		)) : null;
		
		$scheduledPublishTime = $currentMediaItem->scheduled_publish_time;
		$hasAccessibleLiveStream = !is_null($liveStreamItem) && $liveStreamItem->getIsAccessible();
		$hasLiveLiveStream = $hasAccessibleLiveStream && intval($liveStreamItem->getResolvedStateDefinition()->id) === 2;
		$hasFinishedLiveStream = $hasAccessibleLiveStream && intval($liveStreamItem->getResolvedStateDefinition()->id) === 3;
		$currentMediaItem->load("videoItem", "videoItem.chapters");
		$videoItem = $currentMediaItem->videoItem;
		$hasAccessibleVod = !is_null($videoItem) && $videoItem->getIsLive();
		$commentsEnabled = $currentMediaItem->comments_enabled;
		
		$vodPlayStartTime = $this->getVodStartTimeFromUrl();
		
		$vodChapters = array();
		if ($hasAccessibleVod) {
			foreach($videoItem->chapters()->orderBy("time", "asc")->orderBy("title", "asc")->get() as $b=>$a) {
				$vodChapters[] = array(
					"num"		=> $b+1,
					"title"		=> $a->title,
					"timeStr"	=> $a->time_str,
					"time"		=> intval($a->time)
				);
			}
		}
		
		$coverImageResolutions = Config::get("imageResolutions.coverImage");
		$coverImageUri = $playlist->getMediaItemCoverUri($currentMediaItem, $coverImageResolutions['full']['w'], $coverImageResolutions['full']['h']);
		$sideBannerImageResolutions = Config::get("imageResolutions.sideBannerImage");
		$sideBannerUri = $playlist->getMediaItemSideBannerUri($currentMediaItem, $sideBannerImageResolutions['full']['w'], $sideBannerImageResolutions['full']['h']);
		$sideBannerFillImageResolutions = Config::get("imageResolutions.sideBannerImage");
		$sideBannerFillUri = $playlist->getMediaItemSideBannerFillUri($currentMediaItem, $sideBannerFillImageResolutions['full']['w'], $sideBannerFillImageResolutions['full']['h']);
		
		// only autoplay if the user has come from an external site, or specified a start time
		$autoPlay = !is_null($vodPlayStartTime) || !URLHelpers::hasInternalReferrer();
		
		$view->playerInfoUri = $this->getInfoUri($playlist->id, $currentMediaItem->id);
		$view->playlistInfoUri = $this->getPlaylistInfoUri($playlist->id);
		$view->autoContinueMode = $this->getAutoContinueMode();
		$view->registerWatchingUri = $this->getRegisterWatchingUri($playlist->id, $currentMediaItem->id);
		$view->registerLikeUri = $this->getRegisterLikeUri($playlist->id, $currentMediaItem->id);
		$view->adminOverrideEnabled = $userHasMediaItemsPermission;
		$view->loginRequiredMsg = "Please log in to use this feature.";
		$view->autoPlay = $autoPlay;
		$view->vodPlayStartTime = is_null($vodPlayStartTime) ? "" : $vodPlayStartTime;
		$view->vodChapters = $vodChapters;
		$view->commentsEnabled = $commentsEnabled;
		if ($commentsEnabled) {
			$view->getCommentsUri = $this->getGetCommentsUri($currentMediaItem->id);
			$view->postCommentUri = $this->getPostCommentUri($currentMediaItem->id);
			$view->deleteCommentUri = $this->getDeleteCommentUri($currentMediaItem->id);
			$view->canCommentAsFacebookUser = Facebook::isLoggedIn() && Facebook::getUserState() === 0;
			$view->canCommentAsStation = $userHasCommentsPermission;
		}
		$view->streamControlData = $streamControlData;
		$view->mediaItemId = $currentMediaItem->id;
		$view->seriesAd = $seriesAd;
		$view->coverImageUri = $coverImageUri;
		$this->setContent($view, "player", "player", $openGraphProperties, $currentMediaItem->name, 200, $twitterProperties, $sideBannerUri, $sideBannerFillUri);
	}
	
	private function getVodStartTimeFromUrl() {
		if (!isset($_GET['t'])) {
			return null;
		}
		return URLHelpers::convertUrlTimeToSeconds($_GET['t']);
	}
	
	private function getAutoContinueMode() {
		$mode = isset($_GET["autoContinueMode"]) ? $_GET["autoContinueMode"] : "0";
		if ($mode !== "0" && $mode !== "1" && $mode !== "2") {
			return 0;
		}
		return intval($mode);
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
		
		$mediaItem->load("watchingNows", "likes", "liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "videoItem", "videoItem.chapters");
		
		$id = intval($mediaItem->id);
		$title = $playlist->generateEpisodeTitle($mediaItem);
		$uri = $playlist->getMediaItemUri($mediaItem);
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
		$hasVideoItem = !is_null($videoItem) && !is_null($videoItem->sourceFile); // pretend doesn't exist if no video/video processing
		
		$publishTime = $mediaItem->scheduled_publish_time;
		if (!is_null($publishTime)) {
			$publishTime = $publishTime->timestamp;
		}
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		$coverArtUri = $playlist->getMediaItemCoverArtUri($mediaItem, $coverArtResolutions['full']['w'], $coverArtResolutions['full']['h']);
		$hasStream = $hasLiveStreamItem;
		$streamInfoMsg = $hasLiveStreamItem ? $liveStreamItem->information_msg : null;
		$streamState = $hasLiveStreamItem ? intval($liveStreamItem->getResolvedStateDefinition()->id): null;
		$streamEndTime = $streamState === 3 && !is_null($liveStreamItem->end_time) ? $liveStreamItem->end_time->timestamp : null;
		$availableOnDemand = $hasLiveStreamItem ? (boolean) $liveStreamItem->being_recorded : null;
		$externalStreamUrl = $hasLiveStreamItem ? $liveStreamItem->external_stream_url : null;
		$streamViewCount = $hasLiveStreamItem ? intval($liveStreamItem->getViewCount()) : null;
		$hasVod = $hasVideoItem;
		$vodLive = $hasVideoItem ? $videoItem->getIsLive() : null;
		$vodViewCount = $hasVideoItem ? intval($videoItem->getViewCount()) : null;
		$vodChapters = null;
		$vodThumbnails = null;
		if ($hasVideoItem && ($vodLive || $userHasMediaItemsPermission)) {
			$vodChapters = array();
			foreach($videoItem->chapters()->orderBy("time", "asc")->orderBy("title", "asc")->get() as $b=>$a) {
				$vodChapters[] = array(
					"title"		=> $a->title,
					"time"		=> intval($a->time)
				);
			}
			$vodThumbnails = $videoItem->getScrubThumbnails();
		}
		
		
		$minNumberOfViews = Config::get("custom.min_number_of_views");
		if (!$userHasMediaItemsPermission) {
			$viewCountTotal = 0;
			if (!is_null($vodViewCount)) {
				$viewCountTotal += $vodViewCount;
			}
			if (!is_null($streamViewCount)) {
				$viewCountTotal += $streamViewCount;
			}
			if ($viewCountTotal < $minNumberOfViews) {
				// the combined number of views is less than the amount required for it to be sent to the client
				// send null instead
				$vodViewCount = $streamViewCount = null;
			}
		}
		
		$minNumWatchingNow = Config::get("custom.min_num_watching_now");
		$numWatchingNow = $mediaItem->getNumWatchingNow();
		if (!$userHasMediaItemsPermission && $numWatchingNow < $minNumWatchingNow) {
			$numWatchingNow = null;
		}
		
		$user = Facebook::getUser();
		$rememberedPlaybackTime = null;
		if ($hasVideoItem && !is_null($user)) {
			$playbackTime = $videoItem->sourceFile->playbackTimes()->where("user_id", $user->id)->first();
			if (!is_null($playbackTime)) {
				$rememberedPlaybackTime = intval($playbackTime->time);
			}
		}
		$numLikes = $mediaItem->likes_enabled ? $mediaItem->likes()->where("is_like", true)->count() : null;
		$numDislikes = $mediaItem->likes_enabled ? $mediaItem->likes()->where("is_like", false)->count() : null;
		$likeType = null;
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
		// return the uris if there is a live stream which is accessible, and the media item live stream is marked as "live"
		// if the media item is in the "show over" state then just the dvr uris need to be served so the dvr recording can still be watched.
		// note $liveStream is the LiveStream model which is attached to the $liveStreamItem which is a MediaItemLiveStream model.
		if ($hasLiveStreamItem && !is_null($liveStream) && $liveStream->getIsAccessible() && ($streamState === 2 || $streamState === 3 || ($streamState === 1 && $userHasMediaItemsPermission))) {
			$onlyDvrUris = $streamState === 3;
			foreach($liveStreamItem->getQualitiesWithUris($onlyDvrUris ? array("dvrBridge") : array("dvrBridge", "live")) as $qualityWithUris) {
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
		$vodSourceId = null;
		// return the uris (and vod source id) if the item is accessible to the public or the logged in cms user has permission
		if ($hasVideoItem && ($vodLive || $userHasMediaItemsPermission)) {
			$vodSourceId = intval($videoItem->sourceFile->id);
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
			"id"						=> $id,
			"title"						=> $title,
			"uri"						=> $uri,
			"scheduledPublishTime"		=> $publishTime,
			"coverUri"					=> $coverArtUri,
			"embedData"					=> $embedData,
			"hasStream"					=> $hasStream, // true if this media item has a live stream
			"streamInfoMsg"				=> $streamInfoMsg,
			"streamState"				=> $streamState, // 1=pending live, 2=live, 3=stream over, null=no stream
			"streamEndTime"				=> $streamEndTime, // the time the stream was marked as "stream over". null if not "stream over"
			"streamUris"				=> $streamUris,
			"availableOnDemand"			=> $availableOnDemand, // true if the stream is being recorded
			"externalStreamUrl"			=> $externalStreamUrl, // the url to the page containing the live stream if hosted externally
			"streamViewCount"			=> $streamViewCount,
			"hasVod"					=> $hasVod, // true if this media item has a video.
			"vodSourceId"				=> $vodSourceId, // the id of the vod source file.
			"vodLive"					=> $vodLive, // true when the video should be live to the public
			"videoUris"					=> $videoUris,
			"vodViewCount"				=> $vodViewCount,
			"vodChapters"				=> $vodChapters,
			"vodThumbnails"				=> $vodThumbnails,
			"rememberedPlaybackTime"	=> $rememberedPlaybackTime,
			"numWatchingNow"			=> $numWatchingNow,
			"numLikes"					=> $numLikes, // number of likes this media item has
			"numDislikes"				=> $numDislikes, // number of dislikes this media item has
			"likeType"					=> $likeType // "like" if liked, "dislike" if disliked, or null otherwise
		);
		
		return Response::json($data);
	}
	
	public function postRegisterWatching($playlistId, $mediaItemId) {
		$playlist = Playlist::accessibleToPublic()->find($playlistId);
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}

		$success = false;
		if (isset($_POST['playing'])) {
			$playing = $_POST['playing'] === "1";
			$success = $mediaItem->registerWatching($playing);
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
	
	public function postRegisterPlaybackTime($sourceFileId) {
	
		$user = Facebook::getUser();
		if (is_null($user)) {
			App::abort(403); // forbidden
		}
	
		$file = File::find($sourceFileId);
		if (is_null($file)) {
			App::abort(404);
		}
		
		$mediaItemVideo = $file->mediaItemVideoWithFile;
		if (is_null($mediaItemVideo) || !$mediaItemVideo->getIsLive()) {
			App::abort(404);
		}
		
		$success = false;
		
		$time = isset($_POST['time']) ? intval($_POST['time']) : null;
		if (!is_null($time) && $time >= 0) {
			// create/update the record in the database.
			DB::transaction(function() use (&$user, &$file, &$time, &$success) {
				
				$playbackTime = PlaybackTime::where("user_id", $user->id)->where("file_id", $file->id)->lockForUpdate()->first();
				if (!is_null($playbackTime)) {
					// record already exists. Update it
					$playbackTime->time = $time;
					$playbackTime->save();
				}
				else {
					// record doesn't exist. Create it
					$playbackTime = new PlaybackTime(array(
						"time"	=> $time
					));
					$playbackTime->user()->associate($user);
					$playbackTime->file()->associate($file);
					$playbackTime->save();
				}
				$success = true;
			});
			
		}
		return Response::json(array("success"=>$success));
	}
	
	public function postComments($mediaItemId) {
		
		$mediaItem = MediaItem::with("comments", "comments.siteUser")->accessible()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		if (!$mediaItem->comments_enabled) {
			App::abort(403); // forbidden
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
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.siteComments"), 0);
		
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
		
		if (!$mediaItem->comments_enabled) {
			App::abort(403); // forbidden
		}
		
		// true if a user is logged into the cms and has permission to manage comments and post as station.
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.siteComments"), 0);
		
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
		$userHasCommentsPermission = Auth::isLoggedIn() && Auth::getUser()->hasPermission(Config::get("permissions.siteComments"), 0);
		
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
	
	public function postLiveShows() {
		$liveItems = MediaItem::getCachedLiveItems();
		$items = array();
		foreach($liveItems as $a) {
			$items[] = array(
				"id"					=> intval($a['mediaItem']->id),
				"name"					=> $a['generatedName'],
				"scheduledPublishTime"	=> $a['mediaItem']->scheduled_publish_time->timestamp,
				"uri"					=> $a['uri']
			);
		}
		return Response::json(array("items"=>$items));
	}
	
	private function getInfoUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_info_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getPlaylistInfoUri($playlistId) {
		return Config::get("custom.playlist_info_base_uri")."/".$playlistId;
	}
	
	private function getRegisterWatchingUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_register_watching_base_uri")."/".$playlistId ."/".$mediaItemId;
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
