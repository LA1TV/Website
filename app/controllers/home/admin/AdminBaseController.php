<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;
use DebugHelpers;
use Session;
use View;
use MyResponse;
use App;

class AdminBaseController extends BaseController {

	protected $layout = null;
	
	protected function setContent($content, $navPage, $cssPageId, $title=NULL) {
		
		$view = View::make("layouts.home.admin.master");
		
		$view->version = !is_null(DebugHelpers::getVersion()) ? DebugHelpers::getVersion() : "[Unknown]";
		$view->currentNavPage = $navPage;
		$view->cssPageId = $cssPageId;
		$view->title = !is_null($title) ? $title : "LA1TV CMS";
		$view->content = $content;
		$view->description = "The custom built content management system for LA1TV's website.";
		$view->allowRobots = false;
		$view->manifestUri = URL::route('manifest');
		$view->jsFiles = [
			asset("assets/built/admin/commons.chunk.js"),
			asset("assets/built/admin/admin.js")
		];

		$view->pageData = array(
			"baseUrl"		=> URL::to("/"),
			"cookieDomain"	=> Config::get("cookies.domain"),
			"cookieSecure"	=> Config::get("ssl.enabled"),
			"assetsBaseUrl"	=> asset(""),
			"webAppCapable"		=> true,
			"serviceWorkerUrl"	=> URL::route("home-service-worker"),
			"logUri"		=> Config::get("custom.log_uri"),
			"debugId"		=> DebugHelpers::getDebugId(),
			"env"			=> App::environment(),
			"sessionId"		=> Session::getId(),
			"csrfToken"		=> Csrf::getToken(),
			"gaEnabled"		=> Config::get("googleAnalytics.enabled"),
			"version"		=> DebugHelpers::getVersion()
		);
		
		$view->mainMenuItems = array();
		$view->moreMenuItems = array();
		if (Auth::isLoggedIn()) {
			$view->mainMenuItems[] = "dashboard";
			if (Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0)) $view->mainMenuItems[] = "media";
			if (Auth::getUser()->hasPermission(Config::get("permissions.shows"), 0)) $view->mainMenuItems[] = "shows";
			if (Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0)) $view->mainMenuItems[] = "playlists";
			if (Auth::getUser()->hasPermission(Config::get("permissions.liveStreams"), 0)) $view->mainMenuItems[] = "livestreams";
					
			if (Auth::getUser()->hasPermission(Config::get("permissions.siteUsers"), 0)) $view->moreMenuItems[] = "siteusers";
			if (Auth::getUser()->hasPermission(Config::get("permissions.users"), 0)) $view->moreMenuItems[] = "users";
			if (Auth::getUser()->hasPermission(Config::get("permissions.apiUsers"), 0)) $view->moreMenuItems[] = "apiusers";
		}
		
		$response = new MyResponse($view);
		// disable csp for main site because causing too many issues with live streams (and clappr uses unsafe evals etc)
		$response->enableContentSecurityPolicy(false);
		$this->layout = $response;
	}

}
