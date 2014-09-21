<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use URLHelpers;
use uk\co\la1tv\website\models\Playlist;

class PlaylistController extends HomeBaseController {

	public function getIndex($id) {
		
		$playlist = Playlist::with("show", "mediaItems", "relatedItems", "relatedItems.playlists")->accessible()->accessibleToPublic()->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$playlistMediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get();
		
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
				"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
				"playlistName"			=> $playlistName,
				"episodeNo"				=> intval($item->pivot->position) + 1,
				"thumbnailUri"			=> $thumbnailUri,
				"active"				=> false
			);
		}
		
		$relatedItems = $playlist->relatedItems;
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
		$view->escapedPlaylistDescription = !is_null($playlist->description) ? URLHelpers::escapeAndReplaceUrls($playlist->description) : null;
		$view->coverImageUri = $coverUri;
		$view->playlistTableFragment = count($playlistTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> null,
			"tableData"		=> $playlistTableData
		)) : null;
		$view->relatedItemsTableFragment = count($relatedItemsTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> array(
				"title" 		=> "Related Items",
				"navButtons"	=> null
			),
			"tableData"		=> $relatedItemsTableData
		)) : null;
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
