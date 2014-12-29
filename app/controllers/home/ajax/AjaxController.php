<?php namespace uk\co\la1tv\website\controllers\home\ajax;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use Response;
use FormHelpers;
use Config;
use Session;

class AjaxController extends BaseController {

	public function postTime() {
		return Response::json(array(
			"time"	=> microtime(true)
		));
	}
	
	// used as an endpoint to ping to keep a users session alive
	public function postHello() {
		return Response::json(array(
			"data"	=> "hi"
		));
	}
	
	// retrieves log data from javascript running in the clients
	public function postLog() {
	
		$logger = $this->getLogValue(FormHelpers::getValue("logger"));
		$timestamp = $this->formatLogDate(FormHelpers::getValue("timestamp"));
		$level = $this->getLogValue(FormHelpers::getValue("level"));
		$url = $this->getLogValue(FormHelpers::getValue("url"));
		$debugId = $this->getLogValue(FormHelpers::getValue("debug_id"));
		$message = $this->getLogValue(FormHelpers::getValue("message"), true);
	
		$logStr = "Server time: ".$this->formatLogDate(time())."  Session id: \"".Session::getId()."\"  Log level: ".$level."  Client time: ".$timestamp."  Url: ".$url."  Debug id: ".$debugId."  Message: ".$message;
		
		// append to the js log file.
		file_put_contents(Config::get("custom.js_log_file_path"), $logStr . "\r\n", FILE_APPEND | LOCK_EX);
		
		return Response::json(array("success"=>true));
	}
	
	private function formatLogDate($a) {
		$a = intval($a);
		if (is_null($a)) {
			return "[Invalid Date]";
		}
		return '"'.date(DATE_RFC2822, $a).'"';
	}
	
	private function getLogValue($a, $quotesAllowed=false) {
		$str = "[None]";
		
		if (!$quotesAllowed && strpos($a, '"') !== FALSE) {
			// " are not allowed in the value as it's only " that distinguish the separate parts of the log. It's fine in the message as that's the last thing in the log line
			$str = "[Invalid]";
		}
		else if (!is_null($a)) {
			$str = '"'.$a.'"';
		}
		return $str;
	}
}
