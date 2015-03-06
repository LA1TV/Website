<?php

class DebugHelpers {
	
	private static $version = null;
	private static $versionRetrieved = false;
	
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
	
	// get the current version (git commit id) of the app
	public static function getVersion() {
		
		if (self::$versionRetrieved) {
			return self::$version;
		}
		self::$versionRetrieved = true;
		
		$location = realpath(dirname(__FILE__) . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "version");
		if (!is_readable($location)) {
			return null;
		}
		$lines = file($location);
		if ($lines === false || count($lines) < 1) {
			return null;
		}
		$version = trim($lines[0]);
		self::$version = $version;
		return $version;
	}
	
}