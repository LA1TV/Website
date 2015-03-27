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

	// www.la1tv.co.uk
	Route::group(array('domain' => Config::get("subdomains.www"), 'after' => array('setXFrameOptionsHeader')), function() use(&$p) {
		
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
				Route::controller('/admin/quality-definitions', $p.'home\admin\qualityDefinitions\QualityDefinitionsController');
				Route::controller('/admin/siteusers', $p.'home\admin\siteUsers\SiteUsersController');
				Route::controller('/admin/users', $p.'home\admin\users\UsersController');
				Route::controller('/admin/permissions', $p.'home\admin\permissions\PermissionsController');
				Route::controller('/admin', $p.'home\admin\AdminController');
			});
			
			// HOME
			Route::controller('/facebook', $p.'home\facebook\FacebookController');
			// make upload controller also accessible at /file
			Route::controller('/file', $p.'home\admin\upload\UploadController');

			Route::controller('/ajax', $p.'home\ajax\AjaxController');
			Route::controller('/contact', $p.'home\contact\ContactController');
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
			
			// API
			Route::group(array('prefix' => "/api/v1", "before" => array("apiEnabledCheck"), "after" => array("setCorsHeaders")), function() use(&$p) {
				Route::get('service', array("uses"=>$p.'api\v1\ApiController@getService'));
				Route::get('shows/{id}/playlists', array("uses"=>$p.'api\v1\ApiController@getShowPlaylists'));
				Route::get('shows/{id}', array("uses"=>$p.'api\v1\ApiController@getShow'));
				Route::get('shows', array("uses"=>$p.'api\v1\ApiController@getShows'));
				Route::get('playlists/{id}/mediaItems/{id2}', array("uses"=>$p.'api\v1\ApiController@getMediaItem'));
				Route::get('playlists/{id}/mediaItems', array("uses"=>$p.'api\v1\ApiController@getPlaylistMediaItems'));
				Route::get('playlists/{id}', array("uses"=>$p.'api\v1\ApiController@getPlaylist'));
				Route::get('playlists', array("uses"=>$p.'api\v1\ApiController@getPlaylists'));
				
				// show a json 404
				Route::get('{catchAll}', array("uses"=>$p.'api\v1\ApiController@respondNotFound'));
				// handle requests with OPTIONS method
				Route::options('{catchAll}', function() {
					$response = Response::make("", 204); // 204 = No Content
					$response->header("Access-Control-Allow-Origin", "*");
					$response->header("Access-Control-Allow-Methods", "OPTIONS, GET");
					$response->header("Access-Control-Max-Age", 300); // cache preflight request for 5 mins
					$response->header("Access-Control-Allow-Headers", "X-Api-Key");
					return $response;
				});
			});
			
			
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
			
			// /player and /ajax and /file should also be accessible from the embed subdomain as well
			Route::controller('/player', $p.'home\player\PlayerController');
			Route::controller('/ajax', $p.'home\ajax\AjaxController');
			Route::controller('/file', $p.'home\admin\upload\UploadController');
			
			Route::controller('/', $p.'embed\EmbedController');
			// this is here so the named route can be retrieved in PlayerController
			Route::get('/{a}/{b}', array("as"=>"embed-player", "uses"=>$p.'embed\EmbedController@getIndex'));
		});
	});
	
});