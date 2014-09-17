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

// www.la1tv.co.uk
Route::group(array('domain' => Config::get("subdomains.www")), function() use(&$p) {

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
		Route::controller('/about', $p.'home\about\AboutController');
		Route::controller('/contact', $p.'home\contact\ContactController');
		Route::controller('/playlists', $p.'home\playlists\PlaylistsController');
		Route::controller('/player', $p.'home\player\PlayerController');
		Route::controller('/shows', $p.'home\shows\ShowsController');
		Route::controller('/show', $p.'home\show\ShowController');
		Route::controller('/guide', $p.'home\guide\GuideController');
		Route::controller('/', $p.'home\HomeController');
	});
	
});

// embed.la1tv.co.uk
Route::group(array('domain' => Config::get("subdomains.embed")), function() use(&$p) {
	Route::get('/', function() {
		return 'Hello from the embed subdomain!';
	});
});