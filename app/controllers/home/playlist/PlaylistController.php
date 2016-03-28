<?php namespace uk\co\la1tv\website\controllers\home\playlist;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use URLHelpers;
use Config;
use Response;
use Cache;
use uk\co\la1tv\website\models\Playlist;
use PlaylistTableHelpers;

class PlaylistController extends HomeBaseController {

	public function getIndex($id=null) {
		if (is_null($id)) {
			App::abort(404);
		}

		$id = intval($id);
		$cacheKeyPlaylistId = !is_null($id) ? $id : -1;
		$fromCache = Cache::remember("pages.playlist.".$cacheKeyPlaylistId, 15, function() use (&$id) {
		
			$playlist = Playlist::with("show", "mediaItems", "relatedItems", "relatedItems.playlists")->accessibleToPublic()->find(intval($id));
			if (is_null($playlist)) {
				return null;
			}
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
			
			// retrieving inaccessible items as well and then skipping them in the loop. This is so that we get the correct episode number.
			$playlistMediaItems = $playlist->mediaItems()->orderBy("media_item_to_playlist.position")->get();
			
			$playlistTableData = array();
			$activeItemIndex = null;
			foreach($playlistMediaItems as $i=>$item) {
				if (!$item->getIsAccessible()) {
					// this shouldn't be accessible
					continue;
				}
				$thumbnailUri = Config::get("custom.default_cover_uri");
				if (!Config::get("degradedService.enabled")) {
					$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
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
					"title"					=> $item->name,
					"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
					"playlistName"			=> $playlistName,
					"episodeNo"				=> $i+1,
					"thumbnailUri"			=> $thumbnailUri,
					"thumbnailFooter"		=> PlaylistTableHelpers::getFooterObj($item),
					"duration"				=> PlaylistTableHelpers::getDuration($item),
					"active"				=> false,
					"stats"					=> PlaylistTableHelpers::getStatsObj($item)
				);
			}
			
			$relatedItems = $playlist->relatedItems()->accessible()->orderBy("related_item_to_playlist.position")->get();
			$relatedItemsTableData = array();
			foreach($relatedItems as $i=>$item) {
				// a mediaitem can be part of several playlists. Always use the first one that has a show if there is one, or just the first one otherwise
				$relatedItemPlaylist = $item->getDefaultPlaylist();
				$thumbnailUri = Config::get("custom.default_cover_uri");
				if (!Config::get("degradedService.enabled")) {
					$thumbnailUri = $relatedItemPlaylist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
				}
				$relatedItemsTableData[] = array(
					"uri"					=> $relatedItemPlaylist->getMediaItemUri($item),
					"active"				=> false,
					"title"					=> $item->name,
					"escapedDescription"	=> null,
					"playlistName"			=> $relatedItemPlaylist->generateName(),
					"episodeNo"				=> $i+1,
					"thumbnailUri"			=> $thumbnailUri,
					"thumbnailFooter"		=> PlaylistTableHelpers::getFooterObj($item),
					"duration"				=> null,
					"stats"					=> PlaylistTableHelpers::getStatsObj($item)
				);
			}
			
			$coverUri = null;
			$sideBannerUri = null;
			$sideBannerFillUri = null;
			if (!Config::get("degradedService.enabled")) {
				$coverImageResolutions = Config::get("imageResolutions.coverImage");
				$coverUri = $playlist->getCoverUri($coverImageResolutions['full']['w'], $coverImageResolutions['full']['h']);
				$sideBannerImageResolutions = Config::get("imageResolutions.sideBannerImage");
				$sideBannerUri = $playlist->getSideBannerUri($sideBannerImageResolutions['full']['w'], $sideBannerImageResolutions['full']['h']);
				$sideBannerFillImageResolutions = Config::get("imageResolutions.sideBannerFillImage");
				$sideBannerFillUri = $playlist->getSideBannerFillUri($sideBannerFillImageResolutions['full']['w'], $sideBannerFillImageResolutions['full']['h']);
			}
			$playlistName = $playlist->generateName();
			$openGraphCoverArtUri = $playlist->getCoverArtUri($coverArtResolutions['fbOpenGraph']['w'], $coverArtResolutions['fbOpenGraph']['h']);
			$twitterCardCoverArtUri = $playlist->getCoverArtUri($coverArtResolutions['twitterCard']['w'], $coverArtResolutions['twitterCard']['h']);
			
			$twitterProperties = array();
			$twitterProperties[] = array("name"=> "card", "content"=> "summary_large_image");
			
			$openGraphProperties = array();
			if (!is_null($playlist->show)) {
				$openGraphProperties[] = array("name"=> "og:type", "content"=> "video.tv_show");
			}
			if (!is_null($playlist->description)) {
				$twitterProperties[] = array("name"=> "description", "content"=> str_limit($playlist->description, 197, "..."));
				$openGraphProperties[] = array("name"=> "og:description", "content"=> $playlist->description);
			}
			$openGraphProperties[] = array("name"=> "video:release_date", "content"=> $playlist->scheduled_publish_time->toISO8601String());
			$twitterProperties[] = array("name"=> "title", "content"=> $playlistName);
			$openGraphProperties[] = array("name"=> "og:title", "content"=> $playlistName);
			$openGraphProperties[] = array("name"=> "og:image", "content"=> $openGraphCoverArtUri);
			$twitterProperties[] = array("name"=> "image", "content"=> $twitterCardCoverArtUri);
			foreach($playlistTableData as $a) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
			}
			foreach($relatedItemsTableData as $a) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
			}

			$viewProps = array();
			$viewProps['playlistTitle'] = $playlistName;
			$viewProps['escapedPlaylistDescription'] = !is_null($playlist->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($playlist->description)) : null;
			$viewProps['coverImageUri'] = $coverUri;
			$viewProps['seriesUri'] = !is_null($playlist->show) ? $playlist->show->getUri() : null;
			
			$playlistTableFragmentData = count($playlistTableData) > 0 ? array(
				"stripedTable"	=> true,
				"headerRowData"	=> null,
				"tableData"		=> $playlistTableData
			) : null;

			$relatedItemsTableFragmentData = count($relatedItemsTableData) > 0 ? array(
				"stripedTable"	=> true,
				"headerRowData"	=> array(
					"title" 		=> "Related Items",
					"seriesUri"		=> null,
					"navButtons"	=> null
				),
				"tableData"		=> $relatedItemsTableData
			) : null;

			return array(
				"viewProps"						=> $viewProps,
				"playlistTableFragmentData"		=> $playlistTableFragmentData,
				"relatedItemsTableFragmentData"	=> $relatedItemsTableFragmentData,
				"openGraphProperties"			=> $openGraphProperties,
				"twitterProperties"				=> $twitterProperties,
				"sideBannerUri"					=> $sideBannerUri,
				"sideBannerFillUri"				=> $sideBannerFillUri,
				"playlistName"					=> $playlistName
			);
		}, true);

		if (is_null($fromCache)) {
			App::abort(404);
			return;
		}
		
		$cachedViewProps = $fromCache["viewProps"];
		$playlistTableFragmentData = $fromCache["playlistTableFragmentData"];
		$relatedItemsTableFragmentData = $fromCache["relatedItemsTableFragmentData"];
		$openGraphProperties = $fromCache["openGraphProperties"];
		$twitterProperties = $fromCache["twitterProperties"];
		$sideBannerUri = $fromCache["sideBannerUri"];
		$sideBannerFillUri = $fromCache["sideBannerFillUri"];
		$playlistName = $fromCache["playlistName"];

		$view = View::make("home.playlist.index");
		foreach($cachedViewProps as $b=>$a) {
			$view[$b] = $a;
		}

		$view->playlistTableFragment = !is_null($playlistTableFragmentData) ? View::make("fragments.home.playlist", $playlistTableFragmentData) : null;
		$view->relatedItemsTableFragment = !is_null($relatedItemsTableFragmentData) ? View::make("fragments.home.playlist", $relatedItemsTableFragmentData) : null;
		$this->setContent($view, "playlist", "playlist", $openGraphProperties, $playlistName, 200, $twitterProperties, $sideBannerUri, $sideBannerFillUri);
	}
	
	// return json array of items in the playlist in order
	public function postPlaylistInfo($id) {
		$playlist = Playlist::accessibleToPublic()->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$data = array();
		
		$mediaItems = $playlist->mediaItems()->accessible()->orderBy("media_item_to_playlist.position")->get();
		$mediaItems->load("videoItem", "liveStreamItem", "liveStreamItem.stateDefinition");
		foreach($mediaItems as $a) {
			$vod = null;
			$stream = null;
			if (!is_null($a->videoItem)) {
				$available = false;
				if (!Config::get("degradedService.enabled")) {
					$available = $a->videoItem->getIsLive();
				}
				$vod = array(
					"available"	=> $available
				);
			}
			if (!is_null($a->liveStreamItem)) {
				$stream = array(
					"state"	=> intval($a->liveStreamItem->getResolvedStateDefinition()->id)
				);
			}
			$data[] = array(
				"id"		=> intval($a->id),
				"vod"		=> $vod,
				"stream"	=> $stream,
				"url"		=> $playlist->getMediaItemUri($a)
			);
		}
		return Response::json($data);
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
