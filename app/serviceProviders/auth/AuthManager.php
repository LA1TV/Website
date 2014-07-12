<?php namespace uk\co\la1tv\website\serviceProviders\auth;

use uk\co\la1tv\website\serviceProviders\auth\exceptions\NoUserLoggedInException;
use uk\co\la1tv\website\serviceProviders\auth\exceptions\ErrorLoggingOutException;
use uk\co\la1tv\website\serviceProviders\auth\exceptions\UserAlreadyRetrievedException;

use uk\co\la1tv\website\models\User;
use Hash;

class AuthManager {
	
	private $user = null; // contains the user model after is has been requested if a user is logged in
	private $cosignUser = null; // contains the cosign user name after it has been requested
	private $authToken = null; // the hashed version of the token being used to authenticate the user
	
	// returns the user name of the logged in cosign user or null if no cosign user logged in
	public function getCosignUser() {
		
		if ($this->cosignUser) {
			//return cached version
			return $this->cosignUser;
		}
		else if (App::environment() === 'production' && isset($_SERVER["REMOTE_REALM"]) && $_SERVER["REMOTE_REALM"] === "LANCS.LOCAL" &&
			isset($_SERVER["REMOTE_USER"]) && $_SERVER["REMOTE_USER"] !== "") {
			// usernames are case insensitive so might as well convert to lower case
			// to make sure no one can register several times by entering their username with
			// different punctuation. the cosign apache mod might already have done this.
			$this->cosignUser = strtolower($_SERVER["REMOTE_USER"]);
		}
		return $this->cosignUser;
	}

	// gets the user model corresponding to the logged in user
	// returns null if there is not a registered user logged in
	public function getUser() {
		
		// if the user has been requested before returned cached version
		if ($this->user) {
			return $this->user;
		}
		
		// check users table for user with matching session_id
		// if there is one return that user
		
		
		
		if (!is_null($this->authToken)) {
			// attempt to authenticate with token
			$a = $this->getUserModel("auth_token", $this->authToken);
			if (!is_null($a)) {
				if (Hash::needsRehash($a)) {
					// happens if now needs to be converted to a more secure hash (ie more hash cycles/different hash algorithm altogether etc)
					$a->auth_token = Hash::make($a->auth_token);
					if (!$a->save()) {
						$a = null;
					}
				}
			}
		}
		else if (App::environment() === 'production' && $this->getCosignUser()) {
			// attempt to authenticate with cosign user
			$a = $this->getUserModel("cosign_user", $this->getCosignUser());
		}
		else {
			// authentication unsuccessful
			return null;
		}
		if (!is_null($a)) {
			// set this session id 
			$a->session_id = Session::getId(); 
			if (!$a->save()) {
				$a = null;
			}
			$this->user = $a;
		}
		return $this->user;
	}
	
	// retrieves the user model using the $field and $value
	// then if successful sets the session_id in the user model
	private function getUserModel($field, $value) {
		$a = User::where($field, $value)->first();
		if (is_null($a)) {
			return null;
		}
		$a->session_id = Session::getId();
		if (!$a->save()) {
			return null;
		}
		return $a;
	}
	
	// return login URL for redirecting the user to cosign
	public function getLoginUrl($redirectLocation="") {
		return "https://weblogin.lancs.ac.uk/?cosign-http-www2.la1tv.co.uk&http://www2.la1tv.co.uk/".$redirectLocation;
	}
	
	// returns
	// 0: user is in normal active state
	// 1: account disabled by admin. Shouldn't be allowed to use system.
	public function getUserState() {
		if ($this->getUser() === false) {
			// error
			throw(new NoUserLoggedInException());
		}
		if ($this->getUser()->disabled) {
			return 1;
		}
		else {
			return 0;
		}
	}

	// set an auth token that will be used for authentication instead.
	// this takes precedence over cosign
	// must be done before the first successful call of getUser() (or after a user has been logged out)
	public function setAuthToken($authToken) {
		
		if (!is_null($this->user)) {
			// if a user model has already been retrieved then you cannot change the authentication mode
			throw(new UserAlreadyRetrievedException());
		}
		
		$authTokenHashed = Hash::make($authToken);
		
		$this->authToken = $authTokenHashed;
	}
	
	// log the user out of cosign if logged into cosign, or just the site if using token.
	// returns the redirect route that should then be returned from the controller.
	public function logout($redirectLocation="") {
		
		$redirectUrl = url('/').$redirectLocation;
		
		$user = $this->getUser();
		if (is_null($user)) {
			return Redirect::to($redirectUrl);
		}
		
		$user->session_id = null;
		if (!$user->save()) {
			throw(new ErrorLoggingOutException());
		}
		$user = null;
		
		
		if (!$this->getCosignUser()) {
			return Redirect::to($redirectUrl);
		}
		
		// http://www.lancaster.ac.uk/iss/tsg/cosign/using_php.html
		$logoutUrl="https://weblogin.lancs.ac.uk/logout";
		return Redirect::to($logoutUrl."?".$redirectUrl)->withCookie(Cookie::forget($_SERVER['COSIGN_SERVICE']));
	}

}