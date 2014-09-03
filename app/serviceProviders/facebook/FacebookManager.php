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
use Cookie;
use Exception;

class FacebookManager {
	
	private $sessionInitalized = false;
	private $siteUser = null;
	private $siteUserCached = false;
	
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
		$fbSession = null;
		try {
			$fbSession = $helper->getSessionFromRedirect();
		}
		catch(FacebookRequestException $e) {
			return false;
		}
		
		if (is_null($fbSession)) {
			return false;
		}
		
		$token = $fbSession->getAccessToken();
		
		if (!$token->isValid()) {
			return false;
		}
		
		// make a request to get the users uid
		$profile = (new FacebookRequest(
			$fbSession, 'GET', '/me?fields=id'
		))->execute()->getGraphObject(GraphUser::className());

		// lookup user with that uid
		$user = SiteUser::where("fb_uid", $profile->getId())->first();
		if (is_null($user)) {
			// user not logged in before. create new user
			$user = new SiteUser();
			$user->fb_uid = $profile->getId();
			$user->fb_last_update_time = Carbon::now();
			$user->last_seen = Carbon::now();
			// populate the model with the rest of the users information from facebook.
			$this->updateUser($user, $fbSession);
		}
		$user->fb_access_token = (String) $token;
		$this->facebookTokenValid = true;
		$user->save();
		$this->storeAccessToken($token);
	}
	
	public function isLoggedIn() {
		return !is_null($this->getUser());
	}
	
	// returns 0 if user should be able to access access system.
	// returns 1 if users account is banned.
	// throws exception if no user logged in.
	public function getUserState() {
		if (!$this->isLoggedIn()) {
			throw(new Exception("There must be a user logged in in order to get it's state."));
		}
		$user = $this->getUser();
		if ($user->banned) {
			return 1;
		}
		return 0;
	}
	
	// returns the SiteUser model of the user that is logged in or null if no one logged in.
	public function getUser() {
		$this->initFacebookSession();
		
		// a user is logged in if there is an access token stored in the session
		$tokenStr = $this->getStoredAccessToken();
		if (is_null($tokenStr)) {
			return null;
		}
		
		$fbSession = new FacebookSession($tokenStr);
		$token = $fbSession->getAccessToken();
		$user = $this->getUserWithAccessToken($token);
			
		if (is_null($user)) {
			$this->clearStoredAccessToken();
			return;
		}
			
		if ($this->isTimeForNextFacebookUpdate($user)) {
			// check that the token is still valid and hasn't expired. This checks with facebook and fails if user has removed app.
			if (!$token->isValid()) {
				$this->clearStoredAccessToken();
				return;
			}
			$this->updateUser($user, $fbSession);
			$user->save();
		}
		
		return $user;
	}
	
	// updates the user model with information from facebook
	// does not save the model
	private function updateUser($user, $fbSession) {
		$profile = (new FacebookRequest(
			$fbSession, 'GET', '/me?fields=first_name,last_name,name'
		))->execute()->getGraphObject(GraphUser::className());
	
		// add/update details
		$user->first_name = $profile->getFirstName();
		$user->last_name = $profile->getLastName();
		$user->name = $profile->getName();
	}
	
	private function initFacebookSession() {
		if ($this->sessionInitalized) {
			return;
		}
		FacebookSession::setDefaultApplication(Config::get("facebook.appId"), Config::get("facebook.appSecret"));
		$sessionInitalized = true;
	}
	
	private function isTimeForNextFacebookUpdate($user) {
		$timeForUpdate = $user->fb_last_update_time < Carbon::now()->subMinutes(Config::get("facebook.updateInterval"));
		if ($timeForUpdate) {
			$user->fb_last_update_time = Carbon::now();
			$user->save();
		}
		return $timeForUpdate;
	}
	
	private function getStoredAccessToken() {
		$tokenStr = Session::get("facebookAccessToken", null);
		if (is_null($tokenStr)) {
			// check to see if token available in cookie
			$tokenStr = Cookie::get("facebookAccessToken", null);
			if (!is_null($tokenStr)) {
				// copy it accross to session
				Session::set("facebookAccessToken", $tokenStr);
			}
		}
		return $tokenStr;
	}
	
	private function storeAccessToken($token) {
		Session::set("facebookAccessToken", (String) $token);
		// set in a cookie as well as session so if not found in session this can be checked first.
		Cookie::queue(Cookie::forever('facebookAccessToken', (String) $token));
	}
	
	private function clearStoredAccessToken() {
		Session::forget("facebookAccessToken");
		Cookie::queue(Cookie::forget("facebookAccessToken"));
		$this->siteUser = null;
		$this->siteUserCached = false;
	}
	
	private function getUserWithAccessToken($token) {
		// if there's a cached version use that
		if ($this->siteUserCached) {
			// use cached version
			return $this->siteUser;
		}
		
		$siteUser = SiteUser::where("fb_access_token", (String) $token)->first();
		if (!is_null($siteUser)) {
			$this->siteUser = $siteUser;
			$this->siteUserCached = true;
			// update last_seen time
			$siteUser->last_seen = Carbon::now();
			$siteUser->save();
		}
		return $siteUser;
	}
	
	// returns true if successfully logged out
	public function logout() {
		if (!$this->isLoggedIn()) {
			return false;
		}
		$this->clearStoredAccessToken();
		return true;
	}
}