<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use URL;
use Csrf;
use Auth;
use Config;

class AdminBaseController extends BaseController {

	protected $layout = "layouts.home.admin.master";
	
	protected function setContent($content, $navPage, $cssPageId, $title=NULL) {
		$this->layout->baseUrl = URL::to("/");
		$this->layout->currentNavPage = $navPage;
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV CMS";
		$this->layout->csrfToken = Csrf::getToken();
		$this->layout->content = $content;
		
		$this->layout->mainMenuItems = array();
		$this->layout->mainMenuItems[] = "dashboard";
		if (Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0)) $this->layout->mainMenuItems[] = "media";
		if (Auth::getUser()->hasPermission(Config::get("permissions.series"), 0)) $this->layout->mainMenuItems[] = "series";
		if (Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0)) $this->layout->mainMenuItems[] = "playlists";
		if (Auth::getUser()->hasPermission(Config::get("permissions.liveStreams"), 0)) $this->layout->mainMenuItems[] = "livestreams";
				
		$this->layout->moreMenuItems = array();
		if (Auth::getUser()->hasPermission(Config::get("permissions.siteUsers"), 0)) $this->layout->moreMenuItems[] = "siteusers";
		if (Auth::getUser()->hasPermission(Config::get("permissions.users"), 0)) $this->layout->moreMenuItems[] = "users";
		$this->layout->moreMenuItems[] = "monitoring";
	}

}
