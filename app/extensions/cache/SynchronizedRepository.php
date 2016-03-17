<?php namespace uk\co\la1tv\website\extensions\cache;

use Illuminate\Cache\Repository;
use malkusch\lock\mutex\PredisMutex;
use Closure;
use Redis;
use Config;

/*
 * Ensures that the "remember" and "rememberForever" methods are synchronized, so that the
 * cache can only be updated by one process at once. Once the cache
 * has updated if there are other processes waiting they will get
 * the cached value.
 *
 * Also adds support for seconds instead of minutes.
 */
class SynchronizedRepository extends Repository {

	/**
	 * Store an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * @param  bool $isSeconds
	 * @return void
	 */
	public function put($key, $value, $minutes, $isSeconds=false)
	{
		$seconds = null;
		if (!$isSeconds) {
			$minutes = $this->getMinutes($minutes);
			if (!is_null($minutes)) {
				$seconds = $minutes * 60;
			}
		}

		if ( ! is_null($seconds))
		{
			$this->store->put($key, $value, $seconds, true);
		}
	}

	/**
	 * Store an item in the cache if the key does not exist.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * @param  bool $isSeconds
	 * @return bool
	 */
	public function add($key, $value, $minutes, $isSeconds=false)
	{
		if (is_null($this->get($key)))
		{
			$this->put($key, $value, $minutes, $isSeconds); return true;
		}

		return false;
	}

	/**
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string  $key
	 * @param  \DateTime|int  $minutes
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback, $isSeconds=false)
	{
		$lockKey = "SynchronizedRepository.lockKey.".$key;
		$mutex = new PredisMutex([Redis::connection()], $lockKey, Config::get("predisMutex.timeout"));

		$value = null;

		$mutex->check(function() use (&$value, &$key) {
			// If the item exists in the cache we will just return this immediately
			// otherwise we will execute the given Closure and cache the result
			// of that execution for the given number of minutes in storage.
			$value = $this->get($key);

			// return true (meaning cache should be updated), if value not in cache
			// if true is returned then a lock will be obtained, this will run again,
			// and if true is returned again at this point, the "then" callback will run
			// and the cache will be updated
			return is_null($value);
		})->then(function() use (&$value, &$key, &$minutes, &$callback, &$isSeconds) {
			$this->put($key, $value = $callback(), $minutes, $isSeconds);
		});

		return $value;
	}

	/**
	 * Get an item from the cache, or store the default value forever.
	 *
	 * @param  string   $key
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function rememberForever($key, Closure $callback)
	{
		$lockKey = "SynchronizedRepository.lockKey.".$key;
		$mutex = new PredisMutex([Redis::connection()], $lockKey, Config::get("predisMutex.timeout"));

		$value = null;

		$mutex->check(function() use (&$value, &$key) {
			// If the item exists in the cache we will just return this immediately
			// otherwise we will execute the given Closure and cache the result
			// of that execution forever
			$value = $this->get($key);

			// return true (meaning cache should be updated), if value not in cache
			// if true is returned then a lock will be obtained, this will run again,
			// and if true is returned again at this point, the "then" callback will run
			// and the cache will be updated
			return is_null($value);
		})->then(function() use (&$value, &$key, &$callback) {
			$this->forever($key, $value = $callback());
		});

		return $value;
	}
}