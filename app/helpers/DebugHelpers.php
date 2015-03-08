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
		
		$location = app_path() . DIRECTORY_SEPARATOR . "version";
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
	
	public static function shouldSiteBeLive() {
		// site should not be live if the version cannot be retrieved.
		// the version file is removed before the build process starts and recreated when the build process finishes
		return !App::isDownForMaintenance() && (!is_null(self::getVersion()) || App::environment() === "local");
	}
	
	public static function generateMaintenanceModeResponse() {
		$view = View::make("layouts.maintenance.body");
		$view->title = "LA1:TV";
		$view->allowRobots = false;
		$view->version = !is_null(self::getVersion()) ? self::getVersion() : "[Unknown]";
		$view->cssFiles = array(
			asset("/assets/maintenance/css/bootstrap.css"),
			asset("/assets/maintenance/css/main.css")
			
		);
		$view->content = View::make("maintenance", array(
			"developmentEmail"	=> Config::get("contactEmails.development")
		));
		return new MyResponse($view, 503);
	}
	
}