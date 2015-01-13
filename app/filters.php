<?php

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
		if (is_a($response, "Illuminate\Http\Response")) {
			$response->header("Strict-Transport-Security", "max-age=5256000");
		}
	}
});

App::finish(function() {
	// now that the response has been sent to the user fire an event so that code that is now listening for this event can execute
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
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function() {
	if (Request::isMethod('get')) {
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
	if (get_class($response) === "Illuminate\Http\Response") {
		$response->header("X-Frame-Options", "deny");
	}
});

/*
|--------------------------------------------------------------------------
| Content Security Policy Header Filter
|--------------------------------------------------------------------------
*/

Route::filter('setContentSecurityPolicyHeader', function($route, $request, $response) {
	if (is_a($response, "Illuminate\Http\Response")) {
	
		$allowedDomains = array();
		
		if (is_a($response, "MyResponse")) {
			$allowedDomains = array_unique(array_merge($allowedDomains, $response->getContentSecurityPolicyDomains()));
		}
		$domainsString = implode(" ", $allowedDomains);
	
		$response->header("Content-Security-Policy-Report-Only", "default-src 'self' ".$domainsString."; media-src 'self'; script-src 'self' https://www.google-analytics.com; img-src *; frame-src 'none'");
	}
});