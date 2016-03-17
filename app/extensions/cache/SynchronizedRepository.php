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
 */
class SynchronizedRepository extends Repository {

	/**
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string  $key
	 * @param  \DateTime|int  $minutes
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback)
	{
		// timeout after 20 seconds
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
		})->then(function() use (&$value, &$key, &$minutes, &$callback) {
			$this->put($key, $value = $callback(), $minutes);
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
		// timeout after 20 seconds
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