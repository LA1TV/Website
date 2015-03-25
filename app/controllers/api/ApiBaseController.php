<?php namespace uk\co\la1tv\website\controllers\api;

use uk\co\la1tv\website\controllers\BaseController;
use uk\co\la1tv\website\api\ApiResponseData;
use Response;
use Config;
use SmartCache;
use Carbon;
use Log;
use Request;
use ApiAuth;

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
		return $this->setStatusCode(404)->respond([]);
	}
	
	public function respondServerError($message=null) {
		return $this->setStatusCode(500)->respond(["message"=>$message]);
	}
	
	public function respondWithServiceUnavalable($message=null) {
		return $this->setStatusCode(503)->respond(["message"=>$message]);
	}
	
	public function respondNotAuthenticated() {
		return $this->setStatusCode(403)->respond(["message"=>'You are not authenticated. Contact "'.Config::get("contactEmails.development").'" if you need an api key.']);
	}
	
	public function createResponseFromApiResponseData(ApiResponseData $apiResponseData) {
		return $this->setStatusCode($apiResponseData->getStatusCode())->respond($apiResponseData->getData(), $apiResponseData->getTimeCreated());
	}
	
	public function respond($data, $timeDataCreated=null) {
		
		// presume if the status code isn't 200 then the data represents information about
		// an error that has occurred.
		$error = $this->statusCode !== 200;
		
		if (is_null($timeDataCreated)) {
			$timeDataCreated = Carbon::now();
		}
		
		if ($error) {
			if ($this->statusCode === 200) {
				throw(new Exception("Status code cannot be 200 for an error response."));
			}
			
			// if the "message" key is not set then attempt to generate a message from the status code.
			if (empty($data['message'])) {
				if ($this->statusCode === 404) {
					$data['message'] = "Not found!";
				}
				else if ($this->statusCode === 403) {
					$data['message'] = "You are not allowed to access this.";
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
				"timeGenerated"	=> $timeDataCreated->timestamp
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
	
	// $forceRefresh will force cache to be updated
	protected function withCache($key, $seconds, $providerMethod, $providerArgs, $forceRefresh=false) {
		$fullKey = "api.v1:" . $key;
		return SmartCache::get($fullKey, $seconds, "apiResponseDataGenerator", $providerMethod, $providerArgs, $forceRefresh);
	}
	
	protected function log($msg) {
		$tmp = null;
		$user = ApiAuth::getUser();
		if (is_null($user)) {
			$tmp = 'API request from ' . Request::ip();
		}
		else {
			$tmp = 'API request from "' . $user->owner . '" at ' . Request::ip();
		}
		Log::info($tmp . ': ' . $msg);
	}
}
