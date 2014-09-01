<?php namespace uk\co\la1tv\website\controllers\home\facebook;

use uk\co\la1tv\website\controllers\BaseController;
use Config;
use URL;
use Facebook;
use Redirect;

class FacebookController extends BaseController {
	
	// Redirect the user to facebook to login (if necessary)
	public function getLogin() {
		return Facebook::getLoginRedirect(URL::to("/facebook/auth"));
	}
	
	// User bounced back to here from facebook.
	public function anyAuth() {	
		Facebook::authorize();
		return Redirect::to(Config::get("custom.base_url"));
	}
}
