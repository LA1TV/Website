<?php namespace uk\co\la1tv\website\notifications;

use Log;
use Redis;

class MediaItemLiveHandler {

	public function onLive($mediaItemLiveStream) {
		Log::info("In notifications media item live event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->getIsAccessible() || !$mediaItemLiveStream->isLive()) {
			// no longer live or not accessible
			return;
		}
		$this->queueLiveStreamEvent($mediaItemLiveStream);
	}
	
	public function onShowOver($mediaItemLiveStream) {
		Log::info("In notifications media item show over event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->getIsAccessible() || !$mediaItemLiveStream->isOver()) {
			// no longer show over or not accessible
			return;
		}
		$this->queueLiveStreamEvent($mediaItemLiveStream);
	}

	public function onNotLive($mediaItemLiveStream) {
		Log::info("In notifications media item not live event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->getIsAccessible() || !$mediaItemLiveStream->isNotLive()) {
			// no longer not live or not accessible
			return;
		}
		$this->queueLiveStreamEvent($mediaItemLiveStream);
	}

	public function onVodAvailable($mediaItemVideo) {
		Log::info("In notifications media item vod available event handler for MediaItemVideo with ID ".$mediaItemVideo->id.".");
		if (!$mediaItemVideo->getIsLive()) {
			// no longer available
			return;
		}

		$stream = $mediaItemVideo->mediaItem->liveStreamItem;
		if (!is_null($stream) && $stream->getIsAccessible() && $stream->hasDvrRecording()) {
			// don't send notification if this is a recording of a stream and there's a dvr recording
			return;
		}

		$mediaItem = $mediaItemVideo->mediaItem;
		$eventId = "mediaItem.vodAvailable";
		$payload = $this->generatePayload($mediaItem);
		$this->sendToRedis($eventId, $payload);
		Log::info("Sent live event to redis for MediaItemVideo with ID ".$mediaItemVideo->id." (media item with id ".$mediaItem->id.").");
	}

	private function queueLiveStreamEvent($mediaItemLiveStream) {
		$mediaItem = $mediaItemLiveStream->mediaItem;

		$stateDefinition = intval($mediaItemLiveStream->getResolvedStateDefinition()->id);
		$eventId = null;
		if ($stateDefinition === 1) {
			$eventId = "mediaItem.notLive";
		}
		else if ($stateDefinition === 2) {
			$eventId = "mediaItem.live";
		}
		else if ($stateDefinition === 3) {
			$eventId = "mediaItem.showOver";
		}
		else {
			throw(new Exception("Unknown stream state."));
		}
		$payload = $this->generatePayload($mediaItem);
		$this->sendToRedis($eventId, $payload);
		Log::info("Sent live event to redis for MediaItemLiveStream with ID ".$mediaItemLiveStream->id." (media item with id ".$mediaItem->id.").");
	}

	private function generatePayload($mediaItem) {
		$playlist = $mediaItem->getDefaultPlaylist();
		$generatedName = $mediaItem->name;
		if (!is_null($playlist->show)) {
			$generatedName = $playlist->generateName() . ": " . $generatedName;
		}
		$uri = $playlist->getMediaItemUri($mediaItem);
		return array(
			"id"	=> intval($mediaItem->id),
			"name"	=> $generatedName,
			"url"	=> $uri,
			"iconUrl"	=> asset("assets/img/notification-icon.png")
		);
	}

	private function sendToRedis($eventId, $payload) {
		$data = array(
			"eventId"	=> $eventId,
			"payload"	=> $payload
		);
		$redis = Redis::connection();
		$redis->publish("siteNotificationsChannel", json_encode($data));
	}
}