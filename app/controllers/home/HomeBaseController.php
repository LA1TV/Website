<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;
use App;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use Facebook;
use Request;
use MyResponse;
use View;
use URLHelpers;
use DebugHelpers;
use Session;

class HomeBaseController extends BaseController {

	protected $layout = null;
	
	protected function setContent($content, $navPage, $cssPageId, $openGraphProperties=array(), $title=NULL, $statusCode=200, $twitterProperties=null, $sideBannersImageUrl=null, $sideBannersFillImageUrl=null) {
		$description = Config::get("custom.site_description");
	
		$view = View::make("layouts.home.master");
	
		$view->version = !is_null(DebugHelpers::getVersion()) ? DebugHelpers::getVersion() : "[Unknown]";
		$view->baseUrl = URL::to("/");
		$view->currentNavPage = $navPage;
		$view->cssPageId = $cssPageId;
		$view->title = "LA1:TV";
		if (!is_null($title)) {
			$view->title .= ": ".$title;
		}
		$view->description = $description;
		$view->content = $content;
		$view->allowRobots = true;
		$view->cssBootstrap = asset("assets/css/bootstrap/home.css");
		$view->requireJsBootstrap = asset("assets/scripts/bootstrap/home.js");
		$view->loggedIn = Facebook::isLoggedIn();
		$view->sideBannersImageUrl = $sideBannersImageUrl;
		$view->sideBannersFillImageUrl = $sideBannersFillImageUrl;
		$view->sideBannersOn = !is_null($sideBannersImageUrl) || !is_null($sideBannersFillImageUrl);
		$view->supportEmail = Config::get("contactEmails.development");
		$view->pageData = array(
			"baseUrl"		=> URL::to("/"),
			"cookieDomain"	=> Config::get("cookies.domain"),
			"cookieSecure"	=> Config::get("ssl.enabled"),
			"assetsBaseUrl"	=> asset(""),
			"logUri"		=> Config::get("custom.log_uri"),
			"debugId"		=> DebugHelpers::getDebugId(),
			"sessionId"		=> Session::getId(),
			"csrfToken"		=> Csrf::getToken(),
			"loggedIn"		=> Facebook::isLoggedIn(),
			"gaEnabled"		=> Config::get("googleAnalytics.enabled"),
			"env"			=> App::environment(),
			"version"		=> DebugHelpers::getVersion()
		);
		$facebookAppId = Config::get("facebook.appId");
		$defaultOpenGraphProperties = array();
		if (!is_null($facebookAppId)) {
			$defaultOpenGraphProperties[] = array("name"=> "fb:app_id", "content"=> $facebookAppId);
		}
		$defaultOpenGraphProperties[] = array("name"=> "og:title", "content"=> "LA1:TV");
		$defaultOpenGraphProperties[] = array("name"=> "og:url", "content"=> Request::url());
		$defaultOpenGraphProperties[] = array("name"=> "og:locale", "content"=> "en_GB");
		$defaultOpenGraphProperties[] = array("name"=> "og:site_name", "content"=> "LA1:TV");
		$defaultOpenGraphProperties[] = array("name"=> "og:description", "content"=> $description);
		$defaultOpenGraphProperties[] = array("name"=> "og:image", "content"=> Config::get("custom.open_graph_logo_uri"));
		$finalOpenGraphProperties = $this->mergeProperties($defaultOpenGraphProperties, $openGraphProperties);

		$finalTwitterProperties = array();
		if (!is_null($twitterProperties)) {
			$defaultTwitterProperties = array(
				array("name"=> "card", "content"=> "summary"),
				array("name"=> "site", "content"=> "@LA1TV"),
				array("name"=> "description", "content"=> str_limit($description, 197, "...")),
				array("name"=> "image", "content"=> Config::get("custom.twitter_card_logo_uri")),
			);
			$finalTwitterProperties = $this->mergeProperties($defaultTwitterProperties, $twitterProperties);
		}

		$view->openGraphProperties = $finalOpenGraphProperties;
		$view->twitterProperties = $finalTwitterProperties;
		$view->promoAjaxUri = Config::get("custom.live_shows_uri");
		$view->searchQueryAjaxUri = Config::get("custom.search_query_uri");
		
		$view->loginUri = URLHelpers::generateLoginUrl();
		$view->homeUri = Config::get("custom.base_url");
		$view->guideUri = Config::get("custom.base_url") . "/guide";
		$view->blogUri = Config::get("custom.blog_url");
		$view->contactUri = Config::get("custom.base_url") . "/contact";
		$view->accountUri = Config::get("custom.base_url") . "/account";
		$view->adminUri = Config::get("custom.base_url") . "/admin";
		
		// recent shows in dropdown
		$shows = Show::getCachedActiveShows();
		$view->showsDropdown = array();
		foreach($shows as $a) {
			$view->showsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/show/".$a->id, "text"=>$a->name);
		}
		$view->showsUri = Config::get("custom.base_url") . "/shows";
		
		// recent playlists dropdown
		$playlists = Playlist::getCachedActivePlaylists(false);
		$view->playlistsDropdown = array();
		foreach($playlists as $a) {
			$view->playlistsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/playlist/".$a->id, "text"=>$a->name);
		}
		$view->playlistsUri = Config::get("custom.base_url") . "/playlists";
		
		$liveStreams = LiveStream::getCachedSiteLiveStreams();
		$view->liveStreamsDropdown = array();
		foreach($liveStreams as $a) {
			$view->liveStreamsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/livestream/".$a->id, "text"=>$a->name);
		}

		$contentSecurityPolicyDomains = MediaItemLiveStream::getCachedLiveStreamDomains();
		$response = new MyResponse($view, $statusCode);
		// disable csp for main site because causing too many issues with live streams (and clappr uses unsafe evals etc)
		$response->enableContentSecurityPolicy(false);
		//$response->setContentSecurityPolicyDomains($contentSecurityPolicyDomains);
		$this->layout = $response;
	}

	private function mergeProperties($defaultProperties, $properties) {
		$usedNames = array();
		$finalProperties = array();
		foreach($properties as $a) {
			if (!is_null($a['content'])) {
				$finalProperties[] = $a;
			}
			if (!in_array($a['name'], $usedNames)) {
				$usedNames[] = $a['name'];
			}
		}
		foreach($defaultProperties as $a) {
			if (!in_array($a['name'], $usedNames)) {
				$finalProperties[] = $a;
			}
		}
		return $finalProperties;
	}

}
