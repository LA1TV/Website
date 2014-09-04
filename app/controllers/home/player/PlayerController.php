<?php namespace uk\co\la1tv\website\controllers\home\player;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use Response;
use Config;

class PlayerController extends HomeBaseController {

	public function getIndex($id, $mediaItemId=null) {
		
		$playlist = Playlist::with("show", "mediaItems")->find(intval($id));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		if ($playlist->mediaItems->count() === 0) {
			// TODO: no playlist items
			dd("No playlist items. TODO");
		}
		
		$playlistMediaItems = $playlist->mediaItems()->orderBy("media_item_to_playlist.position")->get();
		
		$currentMediaItem = null;
		if (!is_null($mediaItemId)) {
			$currentMediaItem = $playlistMediaItems->find(intval($mediaItemId));
			if (is_null($currentMediaItem)) {
				App:abort(404);
			}
		}
		else {
			$currentMediaItem = $playlistMediaItems[0];
		}
		
		$playlistTableData = array();
		foreach($playlistMediaItems as $item) {
			//$coverArtFile = $item->videoItem->coverArtFile->getImageFileWithResolution(1920, 1080);
			$coverArtFile = null;
			$playlistTableData[] = array(
				"id"			=> $item->id,
				"active"		=> intval($item->id) === intval($currentMediaItem->id),
				"title"			=> $item->name,
				"episodeNo"		=> intval($item->pivot->position) + 1,
				"thumbnailUri"	=> is_null($coverArtFile) ? Config::get("custom.default_cover_uri") : $coverArtFile->getUri()
			);
		}

		$view = View::make("home.player.index");
		$view->episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$view->episodeDescription = $currentMediaItem->description;
		$coverArtFile = $currentMediaItem->videoItem->coverArtFile->getImageFileWithResolution(1920, 1080);
		$view->episodeCoverArtUri = is_null($coverArtFile) ? Config::get("custom.default_cover_uri") : $coverArtFile->getUri();
		$view->episodeUri = $currentMediaItem->videoItem->sourceFile->getVideoFiles()[0]['uri'];
		$view->playlistTitle = $playlist->name;
		
		$view->playlistTableData = $playlistTableData;
		
		
		$this->setContent($view, "player", "player");
	}
	
	// should return ajax response with information for the player.
	// TODO: change to post
	public function anyGetPlayerInfo($playlistId, $mediaItemId) {
		$playlist = Playlist::find($playlistId);
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = MediaItem::with("liveStreamItem", "videoItem")->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$playlist->getMediaItemCoverArtUri($mediaItem);
		
		$publishTime = $mediaItem->scheduled_publish_time;
		if (!is_null($publishTime)) {
			$publishTime = $publishTime->timestamp;
		}
		
		$data = array(
			"scheduledPublishTime"	=> $publishTime,
			"coverUri"				=> null,
			"streamEnabled"			=> true,
			"streamInfoMsg"			=> null,
			"streamState"			=> 2,
			"streamUris"			=> array(
				array(
					"quality"		=> "Auto",
					"uri"			=> "la1tv.co.uk",
					"streamName"	=> "something"
				),
				array(
					"quality"		=> "720p",
					"uri"			=> "la1tv.co.uk",
					"streamName"	=> "something"
				)
			),
			"availableOnDemand"		=> false,
			"vodEnabled"			=> false,
			"vodLive"				=> false,
			"videoUris"			=> array(
				array(
					"quality"	=> "Auto",
					"uris"		=> array(
						"google.com",
						"something.com"
					)
				),
				array(
					"quality"	=> "720p",
					"uris"		=> array(
						"google.com",
						"something.com"
					)
				)
			)
		);
		
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
