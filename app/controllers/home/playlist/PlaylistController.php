<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;
use Config;

class PlaylistController extends HomeBaseController {

	public function getIndex($id, $mediaItemId=null) {
		
		$playlist = Playlist::with("show", "mediaItems")->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		if ($playlist->mediaItems->count() === 0) {
			// TODO: no playlist items
			dd("No playlist items. TODO");
		}
		
		$currentMediaItem = null;
		if (!is_null($mediaItemId)) {
			$currentMediaItem = $playlist->mediaItems->find(intval($mediaItemId));
			if (is_null($currentMediaItem)) {
				App:abort(404);
			}
		}
		else {
			$currentMediaItem = $playlist->mediaItems()->orderBy("media_item_to_playlist.position")->first();
		}
		
		$view = View::make("home.playlist.index");
		$view->episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$view->episodeDescription = $currentMediaItem->description;
		$coverArtFile = $currentMediaItem->videoItem->coverArtFile->getImageFileWithResolution(1920, 1080);
		$view->episodeCoverArtUri = is_null($coverArtFile) ? Config::get("custom.default_cover_uri") : $coverArtFile->getUri();
		$view->episodeUri = $currentMediaItem->videoItem->sourceFile->getVideoFiles()[0]['uri'];
		$view->playlistTitle = $playlist->name;
		$this->setContent($view, "playlist", "playlist");
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
