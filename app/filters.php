<?php

use uk\co\la1tv\website\serviceProviders\apiAuth\exceptions\ApiException;
use uk\co\la1tv\website\serviceProviders\apiAuth\exceptions\ApiNotAuthenticatedException;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/
App::before(function($request)
{
	
	App::error(function(ApiException $exception) {
		return App::make('uk\co\la1tv\website\controllers\api\v1\ApiController')->callAction("respondServerError", array());
	});
	
	App::error(function(ApiNotAuthenticatedException $exception) {
		return App::make('uk\co\la1tv\website\controllers\api\v1\ApiController')->callAction("respondNotAuthenticated", array());
	});
	
	App::missing(function($exception) {
		return App::make('uk\co\la1tv\website\controllers\home\ErrorController')->callAction("generate404", array());
	});

	$maxNestingLevel = ini_get('xdebug.max_nesting_level');
	if (is_null($maxNestingLevel) || $maxNestingLevel === "" || $maxNestingLevel < 200) {
		// when less than 100 was getting error and think it's related to the eloquent whereHas queries referencing other models with similar queries.
		// TODO: look into this to make sure it's not some other reason
		ini_set('xdebug.max_nesting_level', 200);
	}

	if (Config::get("ssl.enabled")) {
		if(!Request::secure()) {
			return Redirect::secure(Request::path(), 301); // permanent redirect
		}
	}

	Cookie::setDefaultPathAndDomain(Config::get("cookies.path"), Config::get("cookies.domain"));
});


App::after(function($request, $response)
{
	
	if (Config::get("ssl.enabled") && Request::secure()) {
		if (method_exists($response, "header")) {
			$response->header("Strict-Transport-Security", "max-age=5256000");
		}
	}
});

App::finish(function() {
	// now that the response has been sent to the user fire an event so that code that is now listening for this event can execute
	// depending on the server configuration the response might still not have been sent though as the server software may wait
	// until the php process finishes before sending the response.
	Event::fire("app.finish");
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
*/

// redirect to login page if not logged in
Route::filter('auth', function() {
	if (is_null(Auth::getUser()) || Auth::getUserState() !== 0) {
		if (Request::wantsJson()) {
			return Response::make("", 401); // unauthorized
		}
		else {
			return Redirect::to("/admin/login")->with("authRequestFromFilter", true);
		}
	}
});

/*
|--------------------------------------------------------------------------
| Home Page Redirect Filter
|--------------------------------------------------------------------------
|
| Redirect to another location if the user gets to the homepage from an
| external location and specified in config.
|
*/

Route::filter('homeRedirect', function() {
	
	$redirectUrl = Config::get("custom.home_redirect_url");
	if (is_null($redirectUrl)) {
		// no url to redirect to
		return;
	}
	
	if (Route::current()->getUri() !== "/") {
		// not home page
		return;
	}
	
	if (isset($_GET['noRedirect'])) {
		// if noRedirect param in url disable redirection
		return;
	}
	
	if (URLHelpers::hasInternalReferrer()) {
		// don't redirect if getting here from another location on the site
		return;
	}
	return Redirect::to($redirectUrl);
});


/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function() {
	if (Request::isMethod('get') || Request::isMethod('options')) {
		return;
	}
	
	// throws exception if token invalid
	Csrf::check();
});


/*
|--------------------------------------------------------------------------
| X-Frame-Options Header Filter
|--------------------------------------------------------------------------
|
| Prevents pages being loaded in an iframe.
|
*/

Route::filter('setXFrameOptionsHeader', function($route, $request, $response) {
	if (method_exists($response, "header")) {
		$response->header("X-Frame-Options", "deny");
	}
});

/*
|--------------------------------------------------------------------------
| Content Security Policy Header Filter
|--------------------------------------------------------------------------
*/

Route::filter('setContentSecurityPolicyHeader', function($route, $request, $response) {
	if (method_exists($response, "header")) {
	
		$allowedDomains = array();
		
		if (is_a($response, "MyResponse")) {
			$allowedDomains = array_unique(array_merge($allowedDomains, $response->getContentSecurityPolicyDomains()));
		}
		$domainsString = implode(" ", $allowedDomains);
	
		$response->header("Content-Security-Policy-Report-Only", "default-src 'self' ".$domainsString."; media-src 'self'; script-src 'self' https://www.google-analytics.com https://platform.twitter.com https://*.twimg.com; img-src *; frame-src https://platform.twitter.com https://syndication.twitter.com; style-src 'self' https://platform.twitter.com/embed");
	}
});

/*
|--------------------------------------------------------------------------
| P3P Header Filter
|--------------------------------------------------------------------------
|
| Adds a p3p header so that ie is happy.
| Otherwise embeddable player has issues with cookies not being saved in ie.
| http://stackoverflow.com/a/16475093/1048589
|
*/

Route::filter('setP3PHeader', function($route, $request, $response) {
	if (method_exists($response, "header")) {
		$response->header("P3P", 'CP="Clifford"');
	}
});

/*
|--------------------------------------------------------------------------
| "Live Check" Filter
|--------------------------------------------------------------------------
|
| Returns the maintenance response if the site should not be live at the
| moment for some reason.
|
*/

Route::filter('liveCheck', function() {
	if (!DebugHelpers::shouldSiteBeLive()) {
		return DebugHelpers::generateMaintenanceModeResponse();
	}
});

/*
|--------------------------------------------------------------------------
| "Api Enabled Check" Filter
|--------------------------------------------------------------------------
|
| Returns a service unavailable response if the api is disabled.
|
*/

Route::filter('apiEnabledCheck', function() {
	if (!Config::get("api.enabled")) {
		return App::make('uk\co\la1tv\website\controllers\api\ApiBaseController')->callAction("respondWithServiceUnavalable", array());
	}
});