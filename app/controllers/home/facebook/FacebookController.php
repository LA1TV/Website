<?php namespace uk\co\la1tv\website\controllers\home\facebook;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use Config;
use URL;
use Facebook;
use Redirect;
use View;
use App;
use Session;
use FormHelpers;

class FacebookController extends HomeBaseController {
	
	private $authUrl;
	
	public function __construct() {
		$this->authUrl = URL::to("/facebook/auth");
	}
	
	// Redirect the user to facebook to login (if necessary)
	public function getLogin() {
		$this->recordReturnUri();
		return Facebook::getLoginRedirect($this->authUrl);
	}
	
	// User bounced back to here from facebook.
	// NOTE: this url (https://www.la1tv.co.uk/facebook/auth) is set in the facebook app settings as a 'valid oauth redirect uri'. If it is changed here it must also be updated there.
	public function anyAuth() {	
		Facebook::authorize();
		return Redirect::to($this->getReturnUri());
	}
	
	public function getLogout() {
		$this->recordReturnUri();
		Facebook::logout();
		return Redirect::to($this->getReturnUri());
	}
	
	// e.g. /request-permission?permissions=email,user_likes
	public function getRequestPermission() {
		if (!isset($_GET['permissions'])) {
			App::abort(400); // bad request
		}
		$permissions = explode(",", $_GET['permissions']);
		if (count($permissions) === 1 && empty($permissions[0])) {
			App::abort(400); // bad request
		}
		$this->recordReturnUri();
		return Facebook::getLoginRedirect($this->authUrl, $permissions);
	}
	
	public function getPermissions() {
		$view = View::make("home.facebook.permissions");
		$this->setContent($view, "fbpermissions", "fbpermissions", array(), "Facebook Permissions");
	}
	
	private function recordReturnUri() {
		Session::set("facebookReturnUri", FormHelpers::getValue("returnuri", "", false, true));	
	}
	
	private function getReturnUri() {
		$returnUri = Config::get("custom.base_url")."/".Session::pull("facebookReturnUri", "");
		if (!filter_var($returnUri, FILTER_VALIDATE_URL)) {
			$returnUri = Config::get("custom.base_url");
		}
		return $returnUri;
	}
}
