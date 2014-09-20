<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;

class PlaylistController extends HomeBaseController {

	public function getIndex($id) {
		
		$playlist = Playlist::with("show", "mediaItems", "relatedItems", "relatedItems.playlists")->accessible()->accessibleToPublic()->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$playlistMediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get();
		if (count($playlistMediaItems) === 0) {
			App:abort(404);
		}
		
		$playlistTableData = array();
		$activeItemIndex = null;
		foreach($playlistMediaItems as $i=>$item) {
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, 1920, 1080);
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
				"title"					=> $item->name,
				"description"			=> "Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description. Some really long description.",
				"playlistName"			=> $playlistName,
				"episodeNo"				=> intval($item->pivot->position) + 1,
				"thumbnailUri"			=> $thumbnailUri
			);
		}
		
		$coverUri = null;
		$coverFile = $playlist->coverFile;
		if (!is_null($coverFile)) {
			$coverFile = $coverFile->getImageFileWithResolution(940, 150);
			if (!is_null($coverFile) && $coverFile->getShouldBeAccessible()) {
				$coverUri = $coverFile->getUri();
			}
		}
		
		$view = View::make("home.playlist.index");
		$view->playlistTitle = $playlist->generateName();
		$view->playlistDescription = $playlist->description;
		$view->coverImageUri = $coverUri;
		$view->playlistTableData = $playlistTableData;
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
