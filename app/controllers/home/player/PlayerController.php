<?php namespace uk\co\la1tv\website\controllers\home\player;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\MediaItem;
use Response;
use Config;

class PlayerController extends HomeBaseController {

	public function getIndex($playlistId, $mediaItemId) {
		
		$playlist = Playlist::with("show", "mediaItems")->find(intval($playlistId));
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$currentMediaItem = $playlist->mediaItems()->find($mediaItemId);
		if (is_null($currentMediaItem)) {
			App::abort(404);
		}
		
		$playlistMediaItems = $playlist->mediaItems()->orderBy("media_item_to_playlist.position")->get();
		
		$playlistTableData = array();
		foreach($playlistMediaItems as $item) {
			$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, 1920, 1080);
			$playlistTableData[] = array(
				"uri"			=> Config::get("custom.player_base_uri")."/".$playlist->id."/".$item->id,
				"active"		=> intval($item->id) === intval($currentMediaItem->id),
				"title"			=> $item->name,
				"episodeNo"		=> intval($item->pivot->position) + 1,
				"thumbnailUri"	=> $thumbnailUri
			);
		}

		$view = View::make("home.player.index");
		$view->episodeTitle = $playlist->generateEpisodeTitle($currentMediaItem);
		$view->episodeDescription = $currentMediaItem->description;
		$view->playlistTitle = $playlist->name;
		$view->playlistTableData = $playlistTableData;
		$view->playerInfoUri = $this->getInfoUri($playlist->id, $currentMediaItem->id);
		$this->setContent($view, "player", "player");
	}
	
	// should return ajax response with information for the player.
	public function postPlayerInfo($playlistId, $mediaItemId) {
		$playlist = Playlist::find($playlistId);
		if (is_null($playlist)) {
			App::abort(404);
		}
		
		$mediaItem = $playlist->mediaItems()->find($mediaItemId);
		if (is_null($mediaItem)) {
			App::abort(404);
		}
		
		$mediaItem->load("liveStreamItem", "liveStreamItem.stateDefinition", "liveStreamItem.liveStream", "liveStreamItem.liveStream", "videoItem");
		
		$liveStreamItem = $mediaItem->liveStreamItem;
		$liveStream = !is_null($liveStreamItem) ? $liveStreamItem->liveStream : null;
		$videoItem = $mediaItem->videoItem;
		
		$publishTime = $mediaItem->scheduled_publish_time;
		if (!is_null($publishTime)) {
			$publishTime = $publishTime->timestamp;
		}
		$coverArtUri = $playlist->getMediaItemCoverArtUri($mediaItem, 1920, 1080);
		$hasStream = !is_null($liveStreamItem);
		$streamInfoMsg = $hasStream ? $liveStreamItem->information_msg : null;
		$streamState = $hasStream ? ($liveStreamItem->enabled ? $liveStreamItem->stateDefinition->id: null) : null;
		$availableOnDemand = $hasStream ? (boolean) $liveStreamItem->being_recorded : null;
		$hasVod = !is_null($videoItem);
		$vodLive = $hasVod ? $videoItem->getIsAccessible() : null;
		
		$streamUris = array();
		if (!is_null($liveStream) && $liveStream->enabled) {
			foreach($liveStream->getUrisWithQualities() as $uriWithQuality) {
				$streamUris[] = array(
					"quality"	=> array(
						"id"	=> intval($uriWithQuality['qualityDefinition']->id),
						"name"	=> $uriWithQuality['qualityDefinition']->name
					),
					"uri"		=> $uriWithQuality['uri']
				);
			}
		}
		
		$videoUris = array();
		if (!is_null($videoItem)) {
			foreach($videoItem->getUrisWithQualities() as $uriWithQuality) {
				$videoUris[] = array(
					"quality"	=> array(
						"id"	=> intval($uriWithQuality['qualityDefinition']->id),
						"name"	=> $uriWithQuality['qualityDefinition']->name
					),
					"uris"		=> array($uriWithQuality['uri']) // this is an array because the front end player supports several different formats for one quality for different browsers. This allows for this if necessary in the future.
				);
			}
		}
		
		$data = array(
			"scheduledPublishTime"	=> $publishTime,
			"coverUri"				=> $coverArtUri,
			"hasStream"				=> $hasStream, // true if this media item has a live stream
			"streamInfoMsg"			=> $streamInfoMsg,
			"streamState"			=> $streamState, // 0=pending live, 1=live, 2=stream over, null=no stream
			"streamUris"			=> $streamUris,
			"availableOnDemand"		=> $availableOnDemand, // true if the stream is being recorded
			"streamViewCount"		=> 999,
			"hasVod"				=> $hasVod, // true if this media item has a video.
			"vodLive"				=> $vodLive, // true when the video should be live to the public
			"videoUris"				=> $videoUris,
			"vodViewCount"			=> 999,
			"numLikes"				=> 20
		);
		
		return Response::json($data);
	}
	
	private function getInfoUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_info_base_uri")."/".$playlistId ."/".$mediaItemId;
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
