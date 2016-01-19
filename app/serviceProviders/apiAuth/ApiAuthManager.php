<?php namespace uk\co\la1tv\website\serviceProviders\apiAuth;

use uk\co\la1tv\website\models\ApiUser;
use uk\co\la1tv\website\serviceProviders\apiAuth\exceptions\ApiNotAuthenticatedException;
use Request;
use Carbon;

class ApiAuthManager {
	
	private $retrievedUser = false;
	private $user = null;
	
	// return the ApiUser that is making the current request or null
	// if no matching api user could be found
	public function getUser() {
		if ($this->retrievedUser) {
			return $this->user;
		}
		// read the key from the request header
		$key = Request::header('X-Api-Key');
		$user = null;
		if (!is_null($key)) {
			$user = ApiUser::where("key", $key)->where("enabled", true)->first();
		}
		if (!is_null($user)) {
			$user->last_request_time = Carbon::now();
			$user->save();
		}	
		$this->user = $user;
		$this->retrievedUser = true;
		return $user;
	}
	
	// throw an ApiNotAuthenticatedException if user is not authenticated.
	public function hasUserOrApiException() {
		if (is_null($this->getUser())) {
			throw(new ApiNotAuthenticatedException());
		}
	}

}