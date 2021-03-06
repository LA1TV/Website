<?php namespace uk\co\la1tv\website\controllers\home\admin\login;

use View;
use FormHelpers;
use Validator;
use Auth;
use App;
use Config;
use Redirect;
use Session;
use URL;

class LoginController extends LoginBaseController {

	public function anyIndex() {
	
		$view = View::make('home.admin.login.index');
		
		// id of the form that's been submitted
		$formSubmitted = isset($_POST['form-submitted']) ? intval($_POST['form-submitted']) : false;
	
		// logout cosign button if logged in as unknown user
		if ($formSubmitted === 3) {
			if (Auth::isLoggedIntoCosign()) {
				return Auth::logoutCosign("/admin/login");
			}
		}
	
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("user", ""),
			array("pass", "")
		), !$formSubmitted);
		
		$errors = null;
		
		if (!Auth::isLoggedIn()) {
			if ($formSubmitted === 1) {
				// logging in with username and password
			
				// attempt to authenticate user
				Auth::login($formData['user'], $formData['pass']);
			
				Validator::extend('logged_in', function($attribute, $value, $parameters) {
					return !is_null(Auth::getUser());
				});
				
				$validator = Validator::make($formData,	array(
					'user'	=> array('required', 'logged_in'),
					'pass'	=> array('required', 'logged_in')
				), array(
					'user.required'		=> FormHelpers::getRequiredMsg(),
					'pass.required'		=> FormHelpers::getRequiredMsg(),
					'user.logged_in'	=> "Either this or the password you entered was incorrect.",
					'pass.logged_in'	=> "Either this or the username you entered was incorrect."
				));
				
				if ($validator->fails()) {
					$errors = $validator->messages();
				}
			}
			else if ($formSubmitted === 2) {
				// user clicked the login with cosign button
				if (!Auth::loginWithCosign()) {
					// the login attempt failed so redirect the user to the cosign login page
					// send them back with a flag so that when they return we can immediatly try and authenticate
					// them instead of them having to click login again
					return Redirect::to(Auth::getLoginUrl("admin/login?fromCosign=1&token=".md5(Session::getId())));
				}
				
			}
			else if (FormHelpers::getValue("fromCosign", null, false, true) === "1" && FormHelpers::getValue("token", null, false, true) === md5(Session::getId())) {
				// the user has returned from cosign so should be authenticated
				// attempt to log them in from cosign
				Auth::loginWithCosign();
			}
		}
		
		if (Auth::isLoggedIn()) {
			$view->accountDisabled = Auth::getUserState() === 1;
		}
		else {
			$view->cosignEnabled = App::environment() === 'production' && Config::get("auth.cosignEnabled");
		}
		
		$formData['pass'] = ""; // never send the password back
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->loggedIn = Auth::isLoggedIn();
		$view->loggedIntoCosignAsUnknownUser = Auth::isLoggedIntoCosign() ? !Auth::currentCosignUserHasAccount() : false;
		$this->setContent($view, "login", "login");
	}
	
	public function anyLogout() {
		// id of the form that's been submitted
		$formSubmitted = isset($_POST['form-submitted']) ? intval($_POST['form-submitted']) : false;
	
		// logout
		if ($formSubmitted === 1) {
			if (Auth::isLoggedIn()) {
				Auth::logout();
			}			
		}
		
		// logout of cosign
		if ($formSubmitted === 2) {
			if (Auth::isLoggedIntoCosign()) {
				return Auth::logoutCosign("/admin/login/logout");
			}
		}
		
		$view = View::make('home.admin.login.logout');
		$view->loggedIn = Auth::isLoggedIn();
		$view->showCosignLogoutButton = Auth::wasCosignUserLoggedIn();
		$this->setContent($view, "logout", "logout");
	}
}
