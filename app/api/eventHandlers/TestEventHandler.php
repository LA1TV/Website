<?php namespace uk\co\la1tv\website\api\eventHandlers;

use Log;
use Redis;

class TestEventHandler {

	public function handle($apiUser) {
		Log::info("In test event handler for api user with ID ".$apiUser->id.".");
		$data = array("apiUserId" => intval($apiUser->id));
		$redis = Redis::connection();
		$redis->publish("testChannel", json_encode($data));
		Log::info("Sent test event to redis for api user with ID ".$apiUser->id.".");
	}
}