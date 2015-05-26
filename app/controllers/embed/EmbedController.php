<?php namespace uk\co\la1tv\website\controllers\embed;

use View;
use Config;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\Playlist;
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
		$this->doResponse($playlist, $mediaItem);
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
		
		$this->doResponse($playlist, $mediaItem);
	}
	
	private function doResponse($playlist, $mediaItem) {
		
		$title = null;
		
		$kioskMode = isset($_GET['kiosk']) && $_GET['kiosk'] === "1";
		$autoPlayVod = $kioskMode || (isset($_GET['autoPlayVod']) && $_GET['autoPlayVod'] === "1");
		$autoPlayStream = $kioskMode || (isset($_GET['autoPlayStream']) && $_GET['autoPlayStream'] === "1");
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
		
		
		// true if a user is logged into the cms and has permission to view media items.
		$userHasMediaItemsPermission = Auth::isLoggedIn() ? Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0) : false;
		
		$title = $mediaItem->name;
		
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
		$view->playerInfoUri = $this->getInfoUri($playlist->id, $mediaItem->id);
		$view->registerViewCountUri = $this->getRegisterViewCountUri($playlist->id, $mediaItem->id);
		$view->registerLikeUri = $this->getRegisterLikeUri($playlist->id, $mediaItem->id);
		$view->updatePlaybackTimeBaseUri = $this->getUpdatePlaybackTimeBaseUri();
		$view->loginRequiredMsg = "Please log in to our website to use this feature.";
		$view->adminOverrideEnabled = $userHasMediaItemsPermission;
		$view->hyperlink = URL::route('player', array($playlist->id, $mediaItem->id));
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
	
	private function getRegisterViewCountUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_player_register_view_count_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getRegisterLikeUri($playlistId, $mediaItemId) {
		return Config::get("custom.embed_player_register_like_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	private function getUpdatePlaybackTimeBaseUri() {
		return Config::get("custom.embed_player_update_playback_time_base_uri");
	}
	
	private function getDisableRedirect() {
		return isset($_GET['disableRedirect']) && $_GET['disableRedirect'] === "1";
	}
	
	public function do404($parameters=array()) {
		$this->do404Response($this->getDisableRedirect());
	}
}
