<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use Cache;
use Carbon;
use Event;
use Queue;
use Redis;
use Closure;
use malkusch\lock\mutex\PredisMutex;
use Jeremeamia\SuperClosure\SerializableClosure;

class SmartCacheManager {
	
	// if the object is cached and not old return cached version.
	// otherwise cache object and return it
	// $forceRefresh will force cache to be updated if it is older than half of the timeout period
	// $providerName is the name registered in the IOC container.
	// $providerMethod is the name of the method to call on the provider
	// $providerMethodArgs is an array of arguments to supply to the provider method
	public function get($key, $seconds, $closure, $forceRefresh=false) {
		// the first time the : must appear must be straight before $key
		// otherwise there could be conflicts
		$keyStart = "smartCache";
		$fullKey = $keyStart . ":" . $key;
	
		$mutex = new PredisMutex([Redis::connection()], $fullKey, 20);
		return $mutex->synchronized(function() use (&$fullKey, &$forceRefresh, &$seconds, &$key, &$closure) {
			// get an updated cached version now in synchronized block
			$responseAndTime = $this->getResponseAndTime($fullKey, $seconds);

			if ($forceRefresh && !is_null($responseAndTime)) {
				if (Carbon::now()->timestamp - $responseAndTime["time"] <= $seconds / 2) {
					// don't force a refresh because the cache isn't older than half the time period
					$forceRefresh = false;
				}
			}
			
			if (!is_null($responseAndTime)) {
				if (Carbon::now()->timestamp - $responseAndTime["time"] > $seconds / 2) {
					// refresh the cache in the background as > half the time has passed
					// before a refresh would be required
					// the app.finish event is fired after the response has been returned to the user.
					Event::listen('app.finish', function() use (&$key, &$seconds, &$closure) {
						Queue::push("uk\co\la1tv\website\serviceProviders\smartCache\SmartCacheQueueJob", [
							"key"			=> $key,
							"seconds"		=> $seconds,
							"closure"		=> serialize(new SerializableClosure($closure))
						]);
					});
				}
			}

			if (is_null($responseAndTime) || $forceRefresh) {
				$responseAndTime = [
					"time"		=> Carbon::now()->timestamp,
					"response"	=> $closure()
				];
				// the cache driver only works in minutes
				Cache::put($fullKey, $responseAndTime, ceil($seconds/60));
			}
			return $responseAndTime["response"];
		});
	}

	private function getResponseAndTime($key, $seconds) {
		// get the cached version if there is one
		$responseAndTime = Cache::get($key, null);
		if (!is_null($responseAndTime)) {
			// check it hasn't expired
			// cache driver only works in minutes which is why this is necessary
			if ($responseAndTime["time"] < Carbon::now()->timestamp - $seconds) {
				// it's expired. pretend it's not in the cache
				$responseAndTime = null;
			}
		}
		return $responseAndTime;
	}
}