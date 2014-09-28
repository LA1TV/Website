<?php namespace uk\co\la1tv\website\controllers\home\facebook;

use uk\co\la1tv\website\controllers\BaseController;
use Config;
use URL;
use Facebook;
use Redirect;
use Session;
use FormHelpers;

class FacebookController extends BaseController {
	
	// Redirect the user to facebook to login (if necessary)
	public function getLogin() {
		$this->recordReturnUri();
		return Facebook::getLoginRedirect(URL::to("/facebook/auth"));
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
