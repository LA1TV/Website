<?php namespace uk\co\la1tv\website\api\eventHandlers;

use Log;
use Redis;

class MediaItemLiveHandler {

	public function onLive($mediaItemLiveStream) {
		Log::info("In API media item live event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->isLive()) {
			// no longer live
			return;
		}
		$this->queueWebhooks($mediaItemLiveStream);
	}
	
	public function onShowOver($mediaItemLiveStream) {
		Log::info("In API media item show over event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->isOver()) {
			// no longer show over
			return;
		}
		$this->queueWebhooks($mediaItemLiveStream);
	}

	public function onNotLive($mediaItemLiveStream) {
		Log::info("In API media item not live event handler for MediaItemLiveStream with ID ".$mediaItemLiveStream->id.".");
		if (!$mediaItemLiveStream->isNotLive()) {
			// no longer not live
			return;
		}
		$this->queueWebhooks($mediaItemLiveStream);
	}

	private function queueWebhooks($mediaItemLiveStream) {
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

		$data = array(
			"eventId"	=> $eventId,
			"payload"	=> array(
				"id"	=> intval($mediaItem->id)
			)
		);

		$redis = Redis::connection();
		$redis->publish("mediaItemLiveChannel", json_encode($data));
		Log::info("Sent live event to redis for MediaItemLiveStream with ID ".$mediaItemLiveStream->id." (media item with id ".$mediaItem->id.").");
	}
}