<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use Facebook;
use Request;

class HomeBaseController extends BaseController {

	protected $layout = "layouts.home.master";
	
	protected function setContent($content, $navPage, $cssPageId, $openGraphProperties=array(), $title=NULL) {
		
		$description = "Lancaster University's Student Union TV station.";
	
		$this->layout->baseUrl = URL::to("/");
		$this->layout->currentNavPage = $navPage;
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV";
		$this->layout->description = $description;
		$this->layout->content = $content;
		$this->layout->allowRobots = true;
		$this->layout->cssBootstrap = asset("assets/css/bootstrap/home.css");
		$this->layout->requireJsBootstrap = asset("assets/scripts/bootstrap/home.js");
		$this->layout->loggedIn = Facebook::isLoggedIn();
		$this->layout->pageData = array(
			"baseUrl"		=> URL::to("/"),
			"assetsBaseUrl"	=> asset(""),
			"csrfToken"		=> Csrf::getToken(),
			"loggedIn"		=> Facebook::isLoggedIn(),
			"gaEnabled"		=> Config::get("googleAnalytics.enabled")
		);
		$facebookAppId = Config::get("facebook.appId");
		$defaultOpenGraphProperties = array();
		if (!is_null($facebookAppId)) {
			$defaultOpenGraphProperties[] = array("name"=> "fb:app_id", "content"=> $facebookAppId);
		}
		$defaultOpenGraphProperties[] = array("name"=> "og:url", "content"=> Request::url());
		$defaultOpenGraphProperties[] = array("name"=> "og:locale", "content"=> "en_GB");
		$defaultOpenGraphProperties[] = array("name"=> "og:site_name", "content"=> "LA1:TV");
		$defaultOpenGraphProperties[] = array("name"=> "og:description", "content"=> $description);
		$defaultOpenGraphProperties[] = array("name"=> "og:image", "content"=> asset("assets/img/logo.png"));
		$usedOpenGraphNames = array();
		$finalOpenGraphProperties = array();
		foreach($openGraphProperties as $a) {
			if (!is_null($a['content'])) {
				$finalOpenGraphProperties[] = $a;
			}
			if (!in_array($a['name'], $usedOpenGraphNames)) {
				$usedOpenGraphNames[] = $a['name'];
			}
		}
		foreach($defaultOpenGraphProperties as $a) {
			if (!in_array($a['name'], $usedOpenGraphNames)) {
				$finalOpenGraphProperties[] = $a;
			}
		}
		$this->layout->openGraphProperties = $finalOpenGraphProperties;
		$this->layout->promoAjaxUri = Config::get("custom.live_shows_uri");
		
		$returnUri = implode("/", Request::segments());
		$this->layout->loginUri = Config::get("custom.base_url") . "/facebook/login?returnuri=".urlencode($returnUri);
		$this->layout->logoutUri = Config::get("custom.base_url") . "/facebook/logout?returnuri=".urlencode($returnUri);
		$this->layout->homeUri = Config::get("custom.base_url");
		$this->layout->guideUri = Config::get("custom.base_url") . "/guide";
		$this->layout->blogUri = Config::get("custom.blog_url");
		$this->layout->contactUri = Config::get("custom.base_url") . "/contact";
		
		// recent shows in dropdown
		$shows = Show::getCachedActiveShows();
		$this->layout->showsDropdown = array();
		foreach($shows as $a) {
			$this->layout->showsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/show/".$a->id, "text"=>$a->name);
		}
		$this->layout->showsUri = Config::get("custom.base_url") . "/shows";
		
		// recent playlists dropdown
		$playlists = Playlist::getCachedActivePlaylists(false);
		$this->layout->playlistsDropdown = array();
		foreach($playlists as $a) {
			$this->layout->playlistsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/playlist/".$a->id, "text"=>$a->name);
		}
		$this->layout->playlistsUri = Config::get("custom.base_url") . "/playlists";
	}

}
