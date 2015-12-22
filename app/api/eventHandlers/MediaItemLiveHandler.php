<?php namespace uk\co\la1tv\website\api\eventHandlers;

use Log;

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
		// TODO push to redis
	}
}