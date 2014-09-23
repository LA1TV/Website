<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use URLHelpers;
use Config;
use uk\co\la1tv\website\models\Playlist;

class PlaylistController extends HomeBaseController {

	public function getIndex($id) {
		
		$playlist = Playlist::with("show", "mediaItems", "relatedItems", "relatedItems.playlists")->accessibleToPublic()->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		$playlistMediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get();
		
		$playlistTableData = array();
		$activeItemIndex = null;
		foreach($playlistMediaItems as $i=>$item) {
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
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
				"title"					=> $playlist->generateEpisodeTitle($item),
				"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
				"playlistName"			=> $playlistName,
				"episodeNo"				=> is_null($playlist->show) ? intval($item->pivot->position) + 1 : null,
				"thumbnailUri"			=> $thumbnailUri,
				"active"				=> false
			);
		}
		
		$relatedItems = $playlist->relatedItems()->accessible()->orderBy("related_item_to_playlist.position")->get();
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
				"thumbnailUri"			=> $thumbnailUri
			);
		}
		
		$coverImageResolutions = Config::get("imageResolutions.coverImage");
		$coverUri = $playlist->getCoverUri($coverImageResolutions['full']['w'], $coverImageResolutions['full']['h']);
		
		$view = View::make("home.playlist.index");
		$view->playlistTitle = $playlist->generateName();
		$view->escapedPlaylistDescription = !is_null($playlist->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($playlist->description)) : null;
		$view->coverImageUri = $coverUri;
		$view->playlistTableFragment = count($playlistTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> null,
			"tableData"		=> $playlistTableData
		)) : null;
		$view->relatedItemsTableFragment = count($relatedItemsTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> array(
				"title" 		=> "Related Items",
				"seriesUri"		=> null,
				"navButtons"	=> null
			),
			"tableData"		=> $relatedItemsTableData
		)) : null;
		$view->seriesUri = !is_null($playlist->show) ? $playlist->show->getUri() : null;
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