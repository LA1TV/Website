<?php namespace uk\co\la1tv\website\controllers\api;

use uk\co\la1tv\website\controllers\BaseController;
use Response;
use Config;

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
			}
			
			if (!isset($data['message'])) {
				throw(new Exception("A message must be supplied for an error response."));
			}
		}
		
		$responseData = array(
			"info"		=> [
				"statusCode"	=> $this->statusCode,
				"time"			=> microtime(true)
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
}
