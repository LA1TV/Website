<?php namespace uk\co\la1tv\website\serviceProviders\facebook;

use uk\co\la1tv\website\models\SiteUser;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Session;
use Config;
use Redirect;
use Request;
use Carbon;

class FacebookManager {
	
	private $sessionInitalized = false;
	private $siteUser = null;
	private $siteUserCached = false;
	
	private function initFacebookSession() {
		if ($this->sessionInitalized) {
			return;
		}
		FacebookSession::setDefaultApplication(Config::get("facebook.appId"), Config::get("facebook.appSecret"));
		$sessionInitalized = true;
	}
	
	public function getLoginRedirect($authUri) {
		$this->initFacebookSession();
		$loginHelper = new FacebookRedirectLoginHelper($authUri);
		return Redirect::to($loginHelper->getLoginUrl());
	}
	
	// should be called on the request back from facebook which contains the authentication info.
	// this will try to authorize the user so that getUser() then returns them.
	// returns true if success or false if error (ie facebook error or user clicked cancel etc)
	public function authorize() {
		$this->initFacebookSession();
		
		$helper = new FacebookRedirectLoginHelper(Request::url());
		$session = null;
		try {
			$session = $helper->getSessionFromRedirect();
		}
		catch(FacebookRequestException $e) {
			return false;
		}
		
		if (is_null($session)) {
			return false;
		}
		
		$token = $session->getAccessToken();
		
		if (!$token->isValid()) {
			return false;
		}
		
		// get the users profile.
		$profile = (new FacebookRequest(
			$session, 'GET', '/me?fields=id,first_name,last_name,name'
		))->execute()->getGraphObject(GraphUser::className());

		// lookup user with that uid
		$user = SiteUser::where("fb_uid", $profile->getId())->first();
		if (is_null($user)) {
			// create new user
			$user = new SiteUser();
		}
		
		// add/update details
		$user->fb_uid = $profile->getId();
		$user->first_name = $profile->getFirstName();
		$user->last_name = $profile->getLastName();
		$user->name = $profile->getName();
		$user->last_seen = Carbon::now();
		$user->save();
		
		Session::set("facebookAccessToken", (String) $token);
		Session::set("loggedInSiteUserId", $user->id);
		$this->siteUser = $user;
		$this->siteUserCached = true;
	}
	
	// returns the SiteUser model of the user that is logged in or null if no one logged in.
	public function getUser() {
		$this->initFacebookSession();
		
		// a user is logged in if there is an access token stored in the session
		$tokenStr = Session::get("facebookAccessToken", null);
		if (is_null($tokenStr)) {
			// no token so definitely not logged in.
			return null;
		}
		
		// check that the token is still valid and hasn't expired
		$session = new FacebookSession($tokenStr);
		$token = $session->getAccessToken();
		if (!$token->isValid()) {
			// token now invalid. Logout user
			$this->logout();
			return null;
		}
		
		// we have a valid token.
		// return the user (if user still exists)
		if ($this->siteUserCached) {
			// use cached version
			return $this->siteUser;
		}
		
		$siteUser = SiteUser::find(Session::get("loggedInSiteUserId"));
		if (!is_null($siteUser)) {
			$this->siteUser = $siteUser;
			$this->siteUserCached = true;
			// update last_seen
			$siteUser->last_seen = Carbon::now();
			$siteUser->save();
		}
		return $siteUser;
	}
	
	public function isLoggedIn() {
		return !is_null($this->getUser());
	}
	
	public function logout() {
		if (!$this->isLoggedIn()) {
			return false;
		}
		Session::forget("facebookAccessToken");
		Session::forget("loggedInSiteUserId");
		$this->siteUser = null;
		$this->siteUserCached = false;
		return true;
	}
}