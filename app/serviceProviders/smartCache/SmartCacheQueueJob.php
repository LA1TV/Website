<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use SmartCache;

class SmartCacheQueueJob {
	
	public function fire($job, $data) {	
		$job->delete();

		$key = $data['key'];
		$seconds = $data['seconds'];
		$closure =  unserialize($data['closure']);
		
		// this will force the cache to be updated.
		SmartCache::get($key, $seconds, $closure, true);
	}

}