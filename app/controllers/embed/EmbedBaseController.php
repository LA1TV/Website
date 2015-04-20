<?php namespace uk\co\la1tv\website\controllers\embed;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Config;
use App;
use View;
use uk\co\la1tv\website\models\LiveStream;
use MyResponse;
use Facebook;
use DebugHelpers;
use Session;

class EmbedBaseController extends BaseController {

	protected $layout = null;
	
	protected function setContent($content, $cssPageId, $title=NULL, $statusCode=200) {
		$view = View::make("layouts.embed.master");
		
		$view->version = !is_null(DebugHelpers::getVersion()) ? DebugHelpers::getVersion() : "[Unknown]";
		$view->baseUrl = URL::to("/");
		$view->cssPageId = $cssPageId;
		$view->title = !is_null($title) ? $title : "LA1:TV";
		$view->description = "";
		$view->content = $content;
		$view->allowRobots = false;
		$view->cssBootstrap = asset("assets/css/bootstrap/embed.css");
		$view->requireJsBootstrap = asset("assets/scripts/bootstrap/embed.js");
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
		
		$contentSecurityPolicyDomains = LiveStream::getCachedLiveStreamDomains();
		$response = new MyResponse($view, $statusCode);
		$response->setContentSecurityPolicyDomains($contentSecurityPolicyDomains);
		$this->layout = $response;
	}

}
