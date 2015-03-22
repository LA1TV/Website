<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use Cache;
use Carbon;
use Closure;
use Event;
use Queue;

class SmartCacheManager {
	
	// if the object is cached and not old return cached version.
	// otherwise cache object and return it
	// $forceRefresh will force cache to be updated
	public function get($key, $seconds, Closure $callback, $forceRefresh=false) {
		// the first time the : must appear must be straight before $key
		// otherwise there could be conflicts
		$keyStart = "smartCache";
		$fullKey = $keyStart . ":" . $key;
		// the key that will exist if the cache item is currently being created
		$creatingCacheKey = $keyStart . ".creating:" . $key;
		$now = Carbon::now()->timestamp;
		
		// time to wait in seconds before presuming item could not be created in cache because
		// there was an issue.
		$creationTimeout = 60;
		$timeStartedCreating = Cache::get($creatingCacheKey, null);
		if (!is_null($timeStartedCreating) && $timeStartedCreating >= $now-$creationTimeout) {
			// no point forcing a refresh as a refresh is already happening,
			// so the latest version will be retrieved anyway
			$forceRefresh = false;
			// wait for cache to contain item, or timeout creating item
			for ($i=0; $i<($creationTimeout-($now-$timeStartedCreating))*10; $i++) {
				usleep(100 * 1000); // 0.1 seconds
				if (is_null(Cache::get($creatingCacheKey, null))) {
					// item created or key removed because timed out
					break;
				}
			}
		}
		
		// get the cached version if there is one
		$responseAndTime = !$forceRefresh ? Cache::get($fullKey, null): null;
		if (!is_null($responseAndTime)) {
			// check it hasn't expired
			// cache driver only works in minutes which is why this is necessary
			if ($responseAndTime["time"] < Carbon::now()->timestamp - $seconds) {
				// it's expired. pretend it's not in the cache
				$responseAndTime = null;
			}
		}
		
	
		
		if (is_null($responseAndTime)) {
			// create the key which will be checked to determine that work is being done.
			// it is possible for this point in the code to be reached by several processes at the same time,
			// but it is unlikely, and if it happens it just means the cache will be updated several times
			// which isn't a huge issue. Otherwise would need to use Semaphores and this gets a bit messy in php
			Cache::put($creatingCacheKey, Carbon::now()->timestamp, ceil($creationTimeout/60));
			$responseAndTime = [
				"time"		=> Carbon::now()->timestamp,
				"response"	=> $callback()
			];
			// the cache driver only works in minutes
			Cache::put($fullKey, $responseAndTime, ceil($seconds/60));
			Cache::forget($creatingCacheKey);
		}
		return $responseAndTime["response"];
	}
}