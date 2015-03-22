<?php namespace uk\co\la1tv\website\controllers\api;

use uk\co\la1tv\website\controllers\BaseController;
use Response;
use Config;
use Cache;
use Carbon;
use Closure;
use Event;

class ApiBaseController extends BaseController {

	private $statusCode = 200;
	private $headers = [];
	private $prettyPrint = true;
	
	public function __construct() {
		// initialise pretty print to what the user specifies in the query string or default to on
		$this->prettyPrint = !isset($_GET['pretty']) || $_GET['pretty'] !== "0";
	}
	
	public function setStatusCode($statusCode) {
		$this->statusCode = intval($statusCode);
		return $this;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	public function setHeaders(array $headers) {
		foreach($headers as $name=>$value) {
			if (!is_string($name) || !is_string($value)) {
				throw(new Exception("Headers invalid."));
			}
		}
		$this->headers = $headers;
		return $this;
	}
	
	public function getHeaders() {
		return $this->headers;
	}
	
	public function enablePrettyPrint($enable) {
		$this->prettyPrint = (boolean) $enable;
		return $this;
	}

	public function isPrettyPrintEnabled() {
		return $this->prettyPrint;
	}
	
	public function respondNotFound() {
		return $this->setStatusCode(404)->respond([], true);
	}
	
	public function respondServerError($message=null) {
		return $this->setStatusCode(500)->respond(["message"=>$message], true);
	}
	
	public function respondWithServiceUnavalable($message=null) {
		return $this->setStatusCode(503)->respond(["message"=>$message], true);
	}
	
	// if $error is true then this will be returned with an "error" key instead of "data" key
	public function respond($data, $error=false) {
		
		if ($error) {
			if ($this->statusCode === 200) {
				throw(new Exception("Status code cannot be 200 for an error response."));
			}
			
			// if the "message" key is not set then attempt to generate a message from the status code.
			if (empty($data['message'])) {
				if ($this->statusCode === 404) {
					$data['message'] = "Not found!";
				}
				else if ($this->statusCode === 500) {
					$data['message'] = 'Server error. Contact "'.Config::get("contactEmails.development").'" for support.';
				}
				else if ($this->statusCode === 503) {
					$data['message'] = 'Service unavailable. Contact "'.Config::get("contactEmails.development").'" for support.';
				}
			}
			
			if (!isset($data['message'])) {
				throw(new Exception("A message must be supplied for an error response."));
			}
		}
		
		$responseData = array(
			"info"		=> [
				"statusCode"	=> $this->statusCode,
				"timeGenerated"	=> microtime(true)
			]
		);
		
		if (!$error) {
			$responseData["data"] = $data;
		}
		else {
			$responseData["error"] = $data;
		}
		return Response::json($responseData, $this->statusCode, $this->headers, $this->prettyPrint ? JSON_PRETTY_PRINT : 0);
	}
	
	// if the response is cached and not old return cached version.
	// otherwise cache response and return it
	// $forceRefresh will force cache to be updated
	protected function withCache($key, $seconds, Closure $callback, $forceRefresh=false) {
		// the first time the : must appear must be straight before $key
		// otherwise there could be conflicts
		$keyStart = "api.v1.".($this->prettyPrint?"1":"0");
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
		
		if (!is_null($responseAndTime)) {
			if (Carbon::now()->timestamp - $responseAndTime["time"] > $seconds / 2) {
				// refresh the cache in the background as > half the time has passed
				// before a refresh would be required
				// the app.finish event is fired after the response has been returned to the user.
				Event::listen('app.finish', function() use (&$key, &$seconds, &$callback) {
					// this will force the cache to be updated.
					$this->withCache($key, $seconds, $callback, true);
				});
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
