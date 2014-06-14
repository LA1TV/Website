<?php namespace uk\co\la1tv\website\serviceProviders\csrf;

use App;
use Session;
use Input;

class CsrfManager {
	
	public function hasValidToken($customName="csrf_token") {
		return Session::token() === Input::get($customName);
	}
	
	public function getToken() {
		return csrf_token();
	}
}