<?php namespace uk\co\la1tv\website\controllers\embed;

use View;
use Config;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\LiveStream;
use URL;
use Auth;
use URLHelpers;

class EmbedController extends EmbedBaseController {
	
	public function handleMediaItemRequest($mediaItemId) {
		$mediaItem = MediaItem::find($mediaItemId);
		if (is_null($mediaItem) || !$mediaItem->getIsAccessible()) {
			$this->do404Response();
			return;
		}
		$playlist = $mediaItem->getDefaultPlaylist();
		if (is_null($playlist)) {
			$this->do404Response();
			return;
		}
		$this->prepareResponse($playlist, $mediaItem);
	}
	
	public function handleRequest($playlistId, $mediaItemId) {
		$playlist = Playlist::with("show", "mediaItems")->accessible()->accessibleToPublic()->find(intval($playlistId));
		$mediaItem = null;
		if (!is_null($playlist)) {
			$mediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		}
		
		if (is_null($mediaItem)) {
			$this->do404Response($this->getDisableRedirect());
			return;
		}
		
		$this->prepareResponse($playlist, $mediaItem);
	}

	public function handleLiveStreamRequest($liveStreamId) {
		$liveStream = LiveStream::showAsLiveStream()->find($liveStreamId);
		if (is_null($liveStream)) {
			$this->do404Response($this->getDisableRedirect());
			return;
		}

		$title = $liveStream->name;
		$playerInfoUri = $this->getLiveStreamInfoUri($liveStream->id);
		$registerWatchingUri = $this->getLiveStreamRegisterWatchingUri($liveStream->id);
		$registerLikeUri = null;
		$adminOverrideEnabled = false;
		$hyperlink = URL::route('liveStream', array($liveStream->id));

		$this->doResponse($title, $playerInfoUri, $registerWatchingUri, $registerLikeUri, $adminOverrideEnabled, $hyperlink);
	}
	
	private function prepareResponse($playlist, $mediaItem) {
		// true if a user is logged into the cms and has permission to view media items.
		$userHasMediaItemsPermission = Auth::isLoggedIn() ? Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0) : false;

		$title = $mediaItem->name;
		$playerInfoUri = $this->getInfoUri($playlist->id, $mediaItem->id);
		$recommendationsUri = $this->getRecommendationsUri($playlist->id, $mediaItem->id);
		$registerWatchingUri = $this->getRegisterWatchingUri($playlist->id, $mediaItem->id);
		$registerLikeUri = $this->getRegisterLikeUri($playlist->id, $mediaItem->id);
		$adminOverrideEnabled = $userHasMediaItemsPermission;
		$hyperlink = URL::route('player', array($playlist->id, $mediaItem->id));

		$this->doResponse($title, $playerInfoUri, $registerWatchingUri, $registerLikeUri, $recommendationsUri, $adminOverrideEnabled, $hyperlink);
	}


	private function doResponse($title, $playerInfoUri, $registerWatchingUri, $registerLikeUri, $recommendationsUri, $adminOverrideEnabled, $hyperlink) {
		
		$kioskMode = isset($_GET['kiosk']) && $_GET['kiosk'] === "1";
		$autoPlayVod = null;
		$autoPlayStream = null;
		// if autoPlay (or autoplay) set then this takes precedense over autoPlayVod and autoPlayStream
		// twitter seems to append "autoplay" with it's player widget when it shows it's own poster
		if ($kioskMode) {
			$autoPlayVod = $autoPlayStream = true;
		}
		else if (isset($_GET['autoPlay'])) {
			$autoPlayVod = $_GET['autoPlay'] === "1";
			$autoPlayStream = $_GET['autoPlay'] === "1";
		}
		else if (isset($_GET['autoplay'])) {
			$autoPlayVod = $_GET['autoplay'] === "1";
			$autoPlayStream = $_GET['autoplay'] === "1";
		}
		else {
			$autoPlayVod = isset($_GET['autoPlayVod']) && $_GET['autoPlayVod'] === "1";
			$autoPlayStream = isset($_GET['autoPlayStream']) && $_GET['autoPlayStream'] === "1";
		}
		$vodPlayStartTime = $kioskMode ? 0 : $this->getVodStartTimeFromUrl();
		$flushMode = $kioskMode || (!isset($_GET['flush']) || $_GET['flush'] === "1");
		$showHeading = !$flushMode && (!isset($_GET['showHeading']) || $_GET['showHeading'] === "1");
		$hideBottomBar = $flushMode;
		$ignoreExternalStreamUrl = $kioskMode || (isset($_GET['ignoreExternalStreamUrl']) && $_GET['ignoreExternalStreamUrl'] === "1");
		$disableFullScreen = $kioskMode || (isset($_GET['disableFullScreen']) && $_GET['disableFullScreen'] === "1");
		$showTitleInPlayer = !$kioskMode && $flushMode;
		$enableSmartAutoPlay = !$kioskMode;
		$disablePlayerControls = $kioskMode;
		$initialVodQualityId = isset($_GET['vodQualityId']) && ctype_digit($_GET['vodQualityId']) ? $_GET['vodQualityId'] : "";
		$initialStreamQualityId = isset($_GET['streamQualityId']) && ctype_digit($_GET['streamQualityId']) ? $_GET['streamQualityId'] : "";

		$view = View::make("embed.player");
		$view->showHeading = $showHeading;
		$view->disableRedirect = $this->getDisableRedirect();
		$view->hideBottomBar = $hideBottomBar;
		$view->autoPlayVod = $autoPlayVod;
		$view->autoPlayStream = $autoPlayStream;
		$view->vodPlayStartTime = is_null($vodPlayStartTime) ? "" : $vodPlayStartTime;
		$view->disableFullScreen = $disableFullScreen;
		$view->showTitleInPlayer = $showTitleInPlayer;
		$view->initialVodQualityId = $initialVodQualityId;
		$view->initialStreamQualityId = $initialStreamQualityId;
		$view->ignoreExternalStreamUrl = $ignoreExternalStreamUrl;
		$view->disablePlayerControls = $disablePlayerControls;
		$view->enableSmartAutoPlay = $enableSmartAutoPlay;
		$view->episodeTitle = $title;
		$view->playerInfoUri = $playerInfoUri;
		$view->recommendationsUri = $recommendationsUri;
		$view->registerWatchingUri = $registerWatchingUri;
		$view->registerLikeUri = $registerLikeUri;
		$view->loginRequiredMsg = "Please log in to our website to use this feature.";
		$view->adminOverrideEnabled = $adminOverrideEnabled;
		$view->hyperlink = $hyperlink;
		$view->hasVideo = true;
		$this->setContent($view, "player", 'LA1:TV- "' . $title . '"');
	}
	
	private function do404Response() {
		$view = View::make("embed.player");
		$view->hyperlink = URL::route('home');
		$view->disableRedirect = $this->getDisableRedirect();
		$view->hasVideo = false;
		$this->setContent($view, "player", 'LA1:TV- [Content Unavailable]', 404);
	}
	
	private function getVodStartTimeFromUrl() {
		if (!isset($_GET['vodPlayStartTime'])) {
			return null;
		}
		return URLHelpers::convertUrlTimeToSeconds($_GET['vodPlayStartTime']);
	}
	
	private function getInfoUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_player_info_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getRecommendationsUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_recommendations_base_uri")."/".$playlistId ."/".$mediaItemId;
	}

	private function getRegisterWatchingUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_player_register_watching_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getRegisterLikeUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_player_register_like_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getLiveStreamInfoUri($liveStreamId) {
		return Config::get("custom.embed_live_stream_player_info_base_uri")."/".$liveStreamId;
	}
	
	private function getLiveStreamRegisterWatchingUri($liveStreamId) {
		return Config::get("custom.embed_live_stream_player_register_watching_base_uri")."/".$liveStreamId;
	}
	
	private function getDisableRedirect() {
		return isset($_GET['disableRedirect']) && $_GET['disableRedirect'] === "1";
	}
	
	public function do404($parameters=array()) {
		$this->do404Response($this->getDisableRedirect());
	}
}
