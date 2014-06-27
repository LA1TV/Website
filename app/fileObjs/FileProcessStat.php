<?php namespace uk\co\la1tv\website\fileObjs;

class FileProcessStat {

	private $stage = null; // integer representing current process stage
	private $percentage = null; // integer (0-100) as percentage representing process completeness
	private $msg = null; // message relating to current stage of processing
	private $error = false; // true if an error has occurred and processing cannot continue. Makes sense to put the reason in $msg in this case
	
	public function setStage($stage) {
		$this->stage = $stage;
	}
	
	public function setPercentage($percentage) {
		$this->percentage = $percentage;
	}
	
	public function setMsg($msg) {
		$this->msg = $msg;
	}
	
	public function setError($error) {
		$this->error = $error;
	}
	
	public function getStage() {
		return $this->stage;
	}
	
	public function getPercentage() {
		return $this->percentage;
	}
	
	public function getMsg() {
		return $this->msg;
	}
	
	public function getError() {
		return $error;
	}
}