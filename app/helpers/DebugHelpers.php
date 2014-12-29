<?php

class DebugHelpers {
	
	public static function getDebugId() {
		$debugIdParam = isset($_GET['debugId']) ? $_GET['debugId'] : null;
		$debugId = null;
		if (!is_null($debugIdParam)) {
			// id must only contain these characters and be between 1 and 50 characters or is rejected
			if (preg_match("/^[A-Za-z0-9\-]{1,50}$/", $debugIdParam) === 1) {
				$debugId = $debugIdParam;
			}			
		}
		return $debugId;
	}
	
}