<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use SmartCache;
use Carbon;
use Log;

class SmartCacheQueueJob {
	
	public function fire($job, $data) {	
		$job->delete();

		$key = $data['key'];
		$seconds = $data['seconds'];
		$expireTime = $data['expireTime'];

		if (Carbon::now()->timestamp >= $expireTime) {
			Log::warning("Smart cache job expired and will therefore not run.");
			return;
		}

		$closure =  unserialize($data['closure']);
		
		// this will force the cache to be updated.
		SmartCache::get($key, $seconds, $closure, true);
	}

}