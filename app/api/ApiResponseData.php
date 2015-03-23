<?php namespace uk\co\la1tv\website\api;

use Carbon;

class ApiResponseData {
	
	private $data = null;
	private $statusCode = null;
	private $timeCreated = null;
	
	public function __construct(array $data, $statusCode=200) {
		$this->data = $data;
		$this->statusCode = $statusCode;
		$this->timeCreated = Carbon::now();
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	public function getTimeCreated() {
		return $this->timeCreated;
	}
}