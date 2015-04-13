<?php namespace uk\co\la1tv\website\serviceProviders\csrf;

use App;
use Session;
use Input;
use Illuminate\Session\TokenMismatchException;

class CsrfManager {
	
	public function hasValidToken($customName=null) {
		if (is_null($customName)) $customName = "csrf_token";
		return Session::token() === Input::get($customName);
	}
	
	public function check($customName=null) {
		if (!$this->hasValidToken($customName)) {
			throw(new TokenMismatchException());
		}
	}
	
	public function getToken() {
		return Session::token();
	}
}