<?php namespace uk\co\la1tv\website\extensions\cache;

use Illuminate\Cache\RedisStore;

/**
 * Same as RedisStore but allows caching in seconds, not minutes.
 */
class ImprovedRedisStore extends RedisStore {

	/**
	 * Store an item in the cache for a given number of minutes (or seconds).
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes, $isSeconds=false)
	{
		$value = is_numeric($value) ? $value : serialize($value);

		if (!$isSeconds) {
			$minutes = max(1, $minutes);
			$seconds = $minutes * 60;
		}
		else {
			$seconds = $minutes;
		}

		$this->connection()->setex($this->prefix.$key, $seconds, $value);
	}

}
