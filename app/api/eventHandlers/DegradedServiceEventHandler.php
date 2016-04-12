<?php namespace uk\co\la1tv\website\api\eventHandlers;

use Log;
use Redis;

class DegradedServiceEventHandler {

	public function onStateChanged($enabled) {
		Log::info("In degraded service state change event handler.");
		$redis = Redis::connection();
		$data = array(
			"eventId"	=> "degradedService.stateChanged",
			"payload"	=> array("enabled" => $enabled)
		);
		$redis->publish("broadcastChannel", json_encode($data));
		Log::info("Sent degraded service state change event to redis.");
	}
}