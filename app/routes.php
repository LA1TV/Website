<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

$p = "uk\\co\\la1tv\\website\\controllers\\";

Route::pattern('slug', '[A-Za-z0-9\-]+');
Route::pattern('catchAll', '.*');
$idRegex = '\d+';
Route::pattern('id', $idRegex);
// not great but can't see another way
Route::pattern('id2', $idRegex);

Route::group(array('before' => array("liveCheck"), 'after' => array('setContentSecurityPolicyHeader', 'setP3PHeader')), function() use(&$p) {

	Route::post('/csp/report', array("as"=>"csp-report", "uses"=>$p.'CspReportingController@report'));

	// www.la1tv.co.uk
	Route::group(array('domain' => Config::get("subdomains.www"), 'before' => array('homeRedirect'), 'after' => array('setXFrameOptionsHeader')), function() use(&$p) {
		
		// API
		Route::group(array('prefix' => "/api/v1", "before" => array("apiEnabledCheck")), function() use(&$p) {
			Route::get('service', array("uses"=>$p.'api\v1\ApiController@getService'));
			Route::get('permissions', array("uses"=>$p.'api\v1\ApiController@getPermissions'));
			Route::get('shows/{id}/playlists', array("uses"=>$p.'api\v1\ApiController@getShowPlaylists'));
			Route::get('shows/{id}', array("uses"=>$p.'api\v1\ApiController@getShow'));
			Route::get('shows', array("uses"=>$p.'api\v1\ApiController@getShows'));
			Route::get('playlists/{id}/mediaItems/{id2}', array("uses"=>$p.'api\v1\ApiController@getPlaylistMediaItem'));
			Route::get('playlists/{id}/mediaItems', array("uses"=>$p.'api\v1\ApiController@getPlaylistMediaItems'));
			Route::get('playlists/{id}', array("uses"=>$p.'api\v1\ApiController@getPlaylist'));
			Route::get('playlists', array("uses"=>$p.'api\v1\ApiController@getPlaylists'));
			Route::get('mediaItems/{id}/playlists', array("uses"=>$p.'api\v1\ApiController@getMediaItemPlaylists'));
			Route::get('mediaItems/{id}', array("uses"=>$p.'api\v1\ApiController@getMediaItem'));
			Route::get('mediaItems', array("uses"=>$p.'api\v1\ApiController@getMediaItems'));
			
			Route::post('webhook/configure', array("uses"=>$p.'api\v1\ApiWebhookController@postConfigure'));
			Route::post('webhook/test', array("uses"=>$p.'api\v1\ApiWebhookController@postTest'));

			// show a json 404
			Route::any('{catchAll}', array("uses"=>$p.'api\v1\ApiController@respondNotFound'));
		});

		Route::group(array('before' => 'csrf'), function() use(&$p) {
			
			// ADMIN
			Route::controller('/admin/login', $p.'home\admin\login\LoginController');
			Route::controller('/admin/upload', $p.'home\admin\upload\UploadController');

			Route::group(array('before' => 'auth'), function() use(&$p) {
				Route::controller('/admin/dashboard', $p.'home\admin\dashboard\DashboardController');
				Route::controller('/admin/media', $p.'home\admin\media\MediaController');
				Route::controller('/admin/shows', $p.'home\admin\shows\ShowsController');
				Route::controller('/admin/playlists', $p.'home\admin\playlists\PlaylistsController');
				Route::controller('/admin/livestreams', $p.'home\admin\livestreams\LiveStreamsController');
				Route::controller('/admin/qualitydefinitions', $p.'home\admin\qualityDefinitions\QualityDefinitionsController');
				Route::controller('/admin/productionroles', $p.'home\admin\productionRoles\ProductionRolesController');
				Route::controller('/admin/siteusers', $p.'home\admin\siteUsers\SiteUsersController');
				Route::controller('/admin/users', $p.'home\admin\users\UsersController');
				Route::controller('/admin/apiusers', $p.'home\admin\apiUsers\ApiUsersController');
				Route::controller('/admin/permissions', $p.'home\admin\permissions\PermissionsController');
				Route::controller('/admin', $p.'home\admin\AdminController');
			});
			
			// HOME
			Route::get('/feeds/latest', array("as"=>"feeds-latest", "uses"=>$p.'home\feeds\FeedsController@getLatest'));
			Route::controller('/facebook', $p.'home\facebook\FacebookController');

			Route::controller('/ajax', $p.'home\ajax\AjaxController');
			// here for named route
			Route::post('/ajax/register-push-notification-endpoint', array("as"=>"ajax-registerPushNotificationEndpoint", "uses"=>$p.'home\ajax\AjaxController@postRegisterPushNotificationEndpoint'));
			Route::controller('/contact', $p.'home\contact\ContactController');
			Route::controller('/livestream', $p.'home\liveStream\LiveStreamController');
			Route::get('/livestream/{a}', array("as"=>"liveStream", "uses"=>$p.'home\liveStream\LiveStreamController@getIndex'));
			Route::controller('/playlists', $p.'home\playlists\PlaylistsController');
			Route::get('/playlists/{a?}', array("as"=>"playlists", "uses"=>$p.'home\playlists\PlaylistsController@getIndex'));
			Route::controller('/playlist', $p.'home\playlist\PlaylistController');
			Route::get('/playlist/{a}', array("as"=>"playlist", "uses"=>$p.'home\playlist\PlaylistController@getIndex'));
			Route::controller('/player', $p.'home\player\PlayerController');
			// this is here so the named route can be retrieved in EmbedController
			Route::get('/player/{a}/{b}', array("as"=>"player", "uses"=>$p.'home\player\PlayerController@getIndex'));
			Route::controller('/shows', $p.'home\shows\ShowsController');
			Route::get('/shows/{a?}', array("as"=>"shows", "uses"=>$p.'home\shows\ShowsController@getIndex'));
			Route::controller('/show', $p.'home\show\ShowController');
			Route::get('/show/{a}', array("as"=>"show", "uses"=>$p.'home\show\ShowController@getIndex'));
			Route::controller('/guide', $p.'home\guide\GuideController');
			Route::get('/guide/{a?}', array("as"=>"guide", "uses"=>$p.'home\guide\GuideController@getIndex'));
			Route::controller('/account', $p.'home\account\AccountController');
			// here for named route
			Route::get('/account', array("as"=>"account", "uses"=>$p.'home\account\AccountController@getIndex'));
			Route::get('/manifest', array("as"=>"manifest", "uses"=>$p.'home\HomeController@getManifest'));
			Route::get('/service-worker', array("as"=>"home-service-worker", "uses"=>$p.'home\HomeController@getServiceWorker'));

			// this must not go higher up as it is important that everything above takes priority
			Route::controller("/{slug}", $p.'home\SlugController');
			
			// this is here so the named route can be retrieved in EmbedController
			Route::get('/', array("as"=>"home", "uses"=>$p.'home\HomeController@getIndex'));
			Route::controller('/', $p.'home\HomeController');
		});
		
	});

	// embed.la1tv.co.uk
	Route::group(array('domain' => Config::get("subdomains.embed")), function() use(&$p) {
		
		Route::group(array('before' => 'csrf'), function() use(&$p) {
			
			// the following should also be accessible from the embed subdomain as well
			Route::post('/mediaitem/player-info/{id}/{id2}', $p.'home\player\PlayerController@postPlayerInfo');
			Route::post('/mediaitem/register-watching/{id}/{id2}', $p.'home\player\PlayerController@postRegisterWatching');
			Route::post('/mediaitem/register-like/{id}/{id2}', $p.'home\player\PlayerController@postRegisterLike');
			Route::post('/mediaitem/register-playback-time/{id}', $p.'home\player\PlayerController@postRegisterPlaybackTime');
			Route::post('/livestream/player-info/{id}', $p.'home\liveStream\LiveStreamController@postPlayerInfo');
			Route::post('/livestream/register-watching/{id}', $p.'home\liveStream\LiveStreamController@postRegisterWatching');
			
			Route::controller('/ajax', $p.'home\ajax\AjaxController');
			
			Route::get('/{id}/{id2}', array("as"=>"embed-player", "uses"=>$p.'embed\EmbedController@handleRequest'));
			Route::get('/{id}', array("as"=>"embed-player-media-item", "uses"=>$p.'embed\EmbedController@handleMediaItemRequest'));
			Route::get('/livestream/{id}', array("as"=>"embed-player-live-stream", "uses"=>$p.'embed\EmbedController@handleLiveStreamRequest'));
			Route::get('{catchAll}', array("uses"=>$p.'embed\EmbedController@do404'));
		});
	});

	// assets.la1tv.co.uk
	Route::group(array('domain' => Config::get("subdomains.assets")), function() use(&$p) {
		Route::get('/file/{id}', array("as"=>"file", "uses"=>$p.'home\admin\upload\UploadController@getIndex'));
	
	});
	
});

// handle requests with OPTIONS method
// this is just a preflight check. The server supports coors requests so allow everything
// the appropriate access control headers still need to be returned from the specific requests
Route::options('{catchAll}', function() {
	$response = Response::make("", 204); // 204 = No Content
	$response->header("Access-Control-Allow-Origin", "*");
	$response->header("Access-Control-Allow-Methods", "OPTIONS, GET");
	$response->header("Access-Control-Allow-Headers", Request::header("Access-Control-Request-Headers"));
	return $response;
});