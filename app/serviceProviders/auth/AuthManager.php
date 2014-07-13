<?php namespace uk\co\la1tv\website\serviceProviders\auth;

use uk\co\la1tv\website\serviceProviders\auth\exceptions\NoUserLoggedInException;
use uk\co\la1tv\website\serviceProviders\auth\exceptions\ErrorLoggingOutException;
use uk\co\la1tv\website\serviceProviders\auth\exceptions\UserAlreadyLoggedInException;

use uk\co\la1tv\website\models\User;
use Hash;

class AuthManager {
	
	private $user = null; // contains the user model after is has been requested if a user is logged in
	private $cosignUser = null; // contains the cosign user name after it has been requested
	private $requestInterval = Config::get("auth.attemptInterval");
	
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
		$this->user = User::where("session_id", Session::getId())->first();
		
		if (is_null($this->user)) {
			// user is not already logged in. (ie no session_id assigned to them)
			// try and log in user from cosign information
			if (App::environment() === 'production' && $this->getCosignUser()) {
				// attempt to authenticate with cosign user
				$a = User::where("cosign_user", $this->getCosignUser())->first();
				if (!is_null($a)) {
					if ($this->authenticateUser($a)) {
						$this->user = $a;
					}
				}
			}
		}
		
		return $this->user;
	}
	
	// return login URL for redirecting the user to cosign
	public function getLoginUrl($redirectLocation="") {
		return "https://weblogin.lancs.ac.uk/?cosign-http-www2.la1tv.co.uk&http://www2.la1tv.co.uk/".$redirectLocation;
	}
	
	// returns
	// 0: user is in normal active state
	// 1: account disabled by admin. Shouldn't be allowed to use system.
	public function getUserState() {
		if (is_null($this->getUser())) {
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

	// attempts to login with a username and password
	// returns true if successful.
	public function login($username, $password) {
		// should be $this->user not getUser() because getUser() will attempt to authenticate with cosign
		if (!is_null($this->user)) {
			throw(new UserAlreadyLoggedInException());
		}
		
		$passwordHash = Hash::make($password);
		
		// find user model and if valid set to $user
		$user = User::where("username", $username)->first();
		if (is_null($user)) {
			$this->doSleep();
			return false;
		}

		if ($user->password_hash !== $passwordHash) {
			$this->doSleep($user->last_login_attempt);
			return false;
		}
		
		if (Hash::needsRehash($passwordHash)) {
			// happens if now needs to be converted to a more secure hash (ie more hash cycles/different hash algorithm altogether etc)
			$user->password_hash = Hash::make($password);
			if (!$user->save()) {
				$this->doSleep($user->last_login_attempt);
				return false;
			}
		}
		if (!$this->authenticateUser($user)) {
			$this->doSleep($user->last_login_attempt);
			return false;
		}
		$this->user = $user;
		return true;
	}
	
	private function authenticateUser(User $user) {
		$user->session_id = Session::getId();
		if (!$user->save()) {
			return false;
		}
		return true;
	}
	
	// log the user out of the site. This does not log the user out of cosign.
	// returns true if successfully logged out
	public function logout() {
		if (is_null($this->user)) {
			// already logged out
			return false;
		}
		$this->user->session_id = null;
		if ($this->user->save()) {
			$this->user = null;
			return true;
		}
		return false;
	}
	
	// returns the redirect route that should then be returned from the controller.
	public function logoutCosign($redirectLocation="") {
		
		$redirectUrl = url('/').$redirectLocation;
		
		if (is_null($this->getCosignUser())) {
			return Redirect::to($redirectUrl);
		}
		
		// http://www.lancaster.ac.uk/iss/tsg/cosign/using_php.html
		$logoutUrl="https://weblogin.lancs.ac.uk/logout";
		return Redirect::to($logoutUrl."?".$redirectUrl)->withCookie(Cookie::forget($_SERVER['COSIGN_SERVICE']));
	}
	
	// only allow a request once every $requestInterval seconds (with a bit of randomness) seconds for a particular user.
	// pass in the time the last request was made.
	// if null is passed in then the sleep will occur for a second
	// the way this works means someone could determine if a user name is correct by measuring the response times, but if they do guess a correct user name brute forcing the password should be infeasible
	public function doSleep($lastAttempt=null) {
		$randAmount = rand(0, 100) * 10000;
		if (is_null($lastAttempt)) {
			usleep(($this->requestInterval * 1000000) + $randAmount);
		}
		else {
			usleep(max(($this->requestInterval - $lastAttempt->diffInSeconds()) * 1000000, 0) + $randAmount);
		}
	}

}