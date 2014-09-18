<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;

class AdminBaseController extends BaseController {

	protected $layout = "layouts.home.admin.master";
	
	protected function setContent($content, $navPage, $cssPageId, $title=NULL) {
		$this->layout->currentNavPage = $navPage;
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV CMS";
		$this->layout->content = $content;
		$this->layout->description = "The custom built content management system for LA1:TV's website.";
		$this->layout->allowRobots = false;
		$this->layout->cssBootstrap = asset("assets/css/bootstrap/admin.css");
		$this->layout->requireJsBootstrap = asset("assets/scripts/bootstrap/admin.js");		
		
		$this->layout->pageData = array(
			"baseUrl"		=> URL::to("/"),
			"assetsBaseUrl"	=> asset(""),
			"csrfToken"		=> Csrf::getToken()
		);
		
		$this->layout->mainMenuItems = array();
		$this->layout->moreMenuItems = array();
		if (Auth::isLoggedIn()) {
			$this->layout->mainMenuItems[] = "dashboard";
			if (Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0)) $this->layout->mainMenuItems[] = "media";
			if (Auth::getUser()->hasPermission(Config::get("permissions.shows"), 0)) $this->layout->mainMenuItems[] = "shows";
			if (Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0)) $this->layout->mainMenuItems[] = "playlists";
			if (Auth::getUser()->hasPermission(Config::get("permissions.liveStreams"), 0)) $this->layout->mainMenuItems[] = "livestreams";
					
			if (Auth::getUser()->hasPermission(Config::get("permissions.siteUsers"), 0)) $this->layout->moreMenuItems[] = "siteusers";
			if (Auth::getUser()->hasPermission(Config::get("permissions.users"), 0)) $this->layout->moreMenuItems[] = "users";
		}
	}

}
