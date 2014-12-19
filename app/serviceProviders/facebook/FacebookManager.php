<?php namespace uk\co\la1tv\website\serviceProviders\facebook;

use uk\co\la1tv\website\models\SiteUser;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use MyFacebookRedirectLoginHelper;
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
	// each element is of form array("accessToken", "facebookSession") facebookSession can be null. accessToken is the facebook access token
	private $cachedFacebookSessions = array();
	
	private $requiredPermissions = array("email");
	
	public function getLoginRedirect($authUri, $requestedPermissionsParam=array()) {
		
		if (!Config::get("facebook.enabled")) {
			throw(new Exception("Facebook login is currently disabled."));
		}

		$requestedPermissions = array_unique(array_merge($this->requiredPermissions, $requestedPermissionsParam));		
		$this->initFacebookSession();
		$loginHelper = new MyFacebookRedirectLoginHelper($authUri);
		return Redirect::to($loginHelper->getLoginUrl($requestedPermissions));
	}
	
	public function getPermissionRequestRedirect($authUri, $requestedPermissionsParam=array()) {
		if (!Config::get("facebook.enabled")) {
			throw(new Exception("Facebook login is currently disabled."));
		}

		$requestedPermissions = array_unique(array_merge($this->requiredPermissions, $requestedPermissionsParam));		
		$this->initFacebookSession();
		$loginHelper = new MyFacebookRedirectLoginHelper($authUri);
		return Redirect::to($loginHelper->getReRequestUrl($requestedPermissions));
	}
	
	public function getShareUri($url) {
		return "https://www.facebook.com/sharer/sharer.php?u=".urlencode($url);
	}
	
	// should be called on the request back from facebook which contains the authentication info.
	// this will try to authorize the user so that getUser() then returns them.
	// returns true if success or false if error (ie facebook error or user clicked cancel etc)
	public function authorize() {
		
		if (!Config::get("facebook.enabled")) {
			throw(new Exception("Cannot authorize facebook user as facebook login is disabled."));
		}
	
		$this->initFacebookSession();
		
		$helper = new MyFacebookRedirectLoginHelper(Request::url());
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
		
		$this->cacheFacebookSession((String) $token, $fbSession);
		
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
			$this->updateUser($user);
		}
		
		if (is_null($user->secret)) {
			// don't have a secret assigned to this user yet. generate one
			// in the future if the user can provide a matching secret this is used to log them in without them having to log into facebook again
			$ourSecret = hash("sha256", str_random(40));
			$user->secret = $ourSecret;
		}
		$this->storeOurSecret($user->secret);
		
		$user->fb_access_token = (String) $token;
		$this->facebookTokenValid = true;
		$user->save();
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
		
		if (!Config::get("facebook.enabled")) {
			// facebook login disabled. pretend no user logged in
			return null;
		}
	
		$this->initFacebookSession();
		
		$secret = $this->getOurStoredSecret();
		if (is_null($secret)) {
			return null;
		}
		
		$user = $this->getUserWithStoredSecret($secret);
		if (is_null($user)) {
			$this->clearOurStoredSecret();
			return;
		}
			
		if ($this->isTimeForNextFacebookUpdate($user)) {
			// $fbSession will be null if it cannot be created for some reason. eg. token expiring.
			$fbSession = $this->getFacebookSession($user);
			if (is_null($fbSession)) {
				$this->clearOurStoredSecret();
				return;
			}
			$this->updateUser($user);
			$user->save();
		}
		
		return $user;
	}
	
	// return the FacebookSession object for a user or null if this was not possible for some reason.
	private function getFacebookSession($user) {
		// first see if a session has already been created for this user. if it has return that.
		if (self::hasCachedFacebookSession($user->fb_access_token)) {
			return self::getCachedFacebookSession($user->fb_access_token);
		}
		$fbSession = new FacebookSession($user->fb_access_token);
		$token = $fbSession->getAccessToken();
		// check that the token is still valid and hasn't expired. This checks with facebook and fails if user has removed app.
		if (!$token->isValid()) {
			// if the token is invalid don't return the session.
			// null should be cached in cachedFacebookSessions so that this check doesn't have to be made again on this request.
			$fbSession = null;
		}
		// store in cache
		$this->cacheFacebookSession($user->fb_access_token, $fbSession);
		return $fbSession;
	}
	
	private function cacheFacebookSession($accessTokenStr, $fbSession) {
		if ($this->hasCachedFacebookSession($accessTokenStr)) {
			// session is already cached. remove it so it can be replaced with this one
			$indexToRemove = null;
			foreach($this->cachedFacebookSessions as $b=>$a) {
				if ($a['accessToken'] === $accessTokenStr) {
					$indexToRemove = $b;
				}
			}
			unset($this->cachedFacebookSessions[$indexToRemove]);
		}
		$this->cachedFacebookSessions[] = array(
			"accessToken"		=> $accessTokenStr,
			"facebookSession"	=> $fbSession
		);
	}
	
	private function getCachedFacebookSession($accessTokenStr) {
		foreach($this->cachedFacebookSessions as $a) {
			if ($a['accessToken'] === $accessTokenStr) {
				return $a['facebookSession'];
			}
		}
		throw(new Exception("No session cached for that access token. You should use hasCachedFacebookSession() to check if one has been cached."));
	}
	
	private function hasCachedFacebookSession($accessTokenStr) {
		foreach($this->cachedFacebookSessions as $a) {
			if ($a['accessToken'] === $accessTokenStr) {
				return true;
			}
		}
		return false;
	}
	
	// returns true if successfully logged out
	public function logout() {
		if (!$this->isLoggedIn()) {
			return false;
		}
		$this->clearOurStoredSecret();
		return true;
	}

	// updates the user model with information from facebook
	// does not save the model
	private function updateUser($user) {
		$this->updateUserOpenGraph($user, true);
	}
	
	// updates the user model with information from opengraph. this includes the users permissions
	// returns true if this succeeds or false otherwise.
	public function updateUserOpenGraph($user, $forceUpdate=false) {
		
		if (!Config::get("facebook.enabled")) {
			throw(new Exception("Facebook login is currently disabled."));
		}
	
		if (!$forceUpdate && !$this->isTimeForNextFacebookUpdate($user)) {
			return true;
		}
	
		$fbSession = $this->getFacebookSession($user);
		if (is_null($fbSession)) {
			return false;
		}
		$profile = (new FacebookRequest(
			$fbSession, 'GET', '/me?fields=first_name,last_name,name,email,permissions'
		))->execute()->getGraphObject(GraphUser::className());
	
		// add/update details
		$user->first_name = $profile->getFirstName();
		$user->last_name = $profile->getLastName();
		$user->name = $profile->getName();
		$user->fb_email = $profile->getEmail();
		$user->fb_last_update_time = Carbon::now();
		
		// update the permissions the user has granted
		$grantedPermissions = array();
		// https://developers.facebook.com/docs/facebook-login/permissions/v2.2
		$ogPermissions = $profile->getProperty("permissions")->asArray();
		foreach($ogPermissions as $a) {
			if ($a->status == "granted") {
				$grantedPermissions[] = $a->permission;
			}
		}
		$user->setFacebookPermissions($grantedPermissions);
		return true;
	}
	
	private function initFacebookSession() {
		if ($this->sessionInitalized) {
			return;
		}
		if (is_null(Config::get("facebook.appId")) || is_null(Config::get("facebook.appSecret"))) {
			throw(new Exception("facebook.appId and/or facebook.appSecret is null. This is only allowed if facebook login is disabled and this is possible by setting facebook.enabled to false."));
		}
		
		FacebookSession::setDefaultApplication(Config::get("facebook.appId"), Config::get("facebook.appSecret"));
		$sessionInitalized = true;
	}
	
	private function isTimeForNextFacebookUpdate($user) {
		return $user->fb_last_update_time < Carbon::now()->subMinutes(Config::get("facebook.updateInterval"));
	}
	
	private function getOurStoredSecret() {
		$secret = Session::get("accountSecret", null);
		if (is_null($secret)) {
			// check to see if secret available in cookie
			$secret = Cookie::get("accountSecret", null);
			if (!is_null($secret)) {
				// copy it across to session
				Session::set("accountSecret", $secret);
			}
		}
		return $secret;
	}
	
	private function storeOurSecret($secret) {
		Session::set("accountSecret", $secret);
		// set in a cookie as well as session so if not found in session this can be checked first.
		Cookie::queue(Cookie::forever('accountSecret', $secret, null, null, Config::get("ssl.enabled")));
	}
	
	private function clearOurStoredSecret() {
		Session::forget("accountSecret");
		Cookie::queue(Cookie::forget("accountSecret"));
		$this->siteUser = null;
		$this->siteUserCached = false;
	}
	
	private function getUserWithStoredSecret($secret) {
		// if there's a cached version use that
		if ($this->siteUserCached) {
			// use cached version
			return $this->siteUser;
		}
		
		$siteUser = SiteUser::where("secret", $secret)->first();
		if (!is_null($siteUser)) {
			$this->siteUser = $siteUser;
			$this->siteUserCached = true;
			// update last_seen time
			$siteUser->last_seen = Carbon::now();
			$siteUser->save();
		}
		return $siteUser;
	}
}