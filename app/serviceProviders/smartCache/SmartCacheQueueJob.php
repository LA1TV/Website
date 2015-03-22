<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use SmartCache;

class SmartCacheQueueJob extends Facade {
	
	public function fire($job, $data) {	
		$job->delete();

		$key = $data['key'];
		$seconds = $data['seconds'];
		$callback = $data['callback'];
		
		// this will force the cache to be updated.
		SmartCache::get($key, $seconds, $callback, true);
	}

}