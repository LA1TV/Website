<?php namespace uk\co\la1tv\website\controllers\embed;

use View;
use Config;
use uk\co\la1tv\website\models\Playlist;
use URL;
use Auth;

class EmbedController extends EmbedBaseController {

	public function getIndex($playlistId, $mediaItemId) {
	
		$playlist = Playlist::with("show", "mediaItems")->accessible()->accessibleToPublic()->find(intval($playlistId));
		$currentMediaItem = null;
		if (!is_null($playlist)) {
			$currentMediaItem = $playlist->mediaItems()->accessible()->find($mediaItemId);
		}
		
		$view = View::make("embed.player");
		$title = null;
		
		if (is_null($currentMediaItem)) {
			$title = "LA1:TV- [Content Unavailable]";
			$view->hyperlink = URL::route('home');
			$view->hasVideo = false;
		}
		else {
			// true if a user is logged into the cms and has permission to view media items.
			$userHasMediaItemsPermission = Auth::isLoggedIn() ? Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0) : false;
	
			$title = $currentMediaItem->name;
			
			$flushMode = isset($_GET['flush']) && $_GET['flush'] === "1";
			$showHeading = !$flushMode && (!isset($_GET['showHeading']) || $_GET['showHeading'] === "1");
			$hideBottomBar = $flushMode;
			$ignoreExternalStreamUrl = isset($_GET['ignoreExternalStreamUrl']) && $_GET['ignoreExternalStreamUrl'] === "1";
			
			$view->showHeading = $showHeading;
			$view->hideBottomBar = $hideBottomBar;
			$view->ignoreExternalStreamUrl = $ignoreExternalStreamUrl;
			$view->episodeTitle = $title;
			$view->playerInfoUri = $this->getInfoUri($playlistId, $mediaItemId);
			$view->registerViewCountUri = $this->getRegisterViewCountUri($playlistId, $mediaItemId);
			$view->registerLikeUri = $this->getRegisterLikeUri($playlistId, $mediaItemId);
			$view->updatePlaybackTimeBaseUri = $this->getUpdatePlaybackTimeBaseUri();
			$view->loginRequiredMsg = "Please log in to our website to use this feature.";
			$view->adminOverrideEnabled = $userHasMediaItemsPermission;
			$view->hyperlink = URL::route('player', array($playlistId, $mediaItemId));
			$view->hasVideo = true;
		}
		$this->setContent($view, "player", 'LA1:TV- "' . $title . '"');
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
