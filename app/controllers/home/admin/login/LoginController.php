<?php namespace uk\co\la1tv\website\controllers\home\admin\login;

use View;
use FormHelpers;
use Csrf;
use Validator;
use Auth;
use App;
use Config;

class LoginController extends LoginBaseController {

	public function anyIndex() {
	
		$view = View::make('home.admin.login.index');
		
		$loggedIn = !is_null(Auth::getUser());
		
		// id of the form that's been submitted
		$formSubmitted = isset($_POST['form-submitted']) ? intval($_POST['form-submitted']) : false;
	
		if ($formSubmitted) {
			// throws exception if token invalid
			Csrf::check();
		};
	
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("user", ""),
			array("pass", "")
		), !$formSubmitted);
		
		$errors = null;
		
		if ($formSubmitted === 1 && !$loggedIn) {

			// attempt to authenticate user
			Auth::login($formData['user'], $formData['pass']);
		
			Validator::extend('logged_in', function($attribute, $value, $parameters) {
				if ($value === "") {
					return true;
				}
				return !is_null(Auth::getUser());
			});
		
			$validator = Validator::make($formData,	array(
				'user'	=> array('required', 'logged_in'),
				'pass'	=> array('logged_in')
			), array(
				'user.required'		=> FormHelpers::getRequiredMsg(),
				'user.logged_in'	=> "Either this or the password you entered was incorrect.",
				'pass.logged_in'	=> "Either this or the username you entered was incorrect."
			));
			
			if ($validator->fails()) {
				$errors = $validator->messages();
			}
			else {
				$loggedIn = true;
			}
		}
		
		if ($loggedIn) {
			$view->accountDisabled = $this->getUser()->getUserState() === 1;
		}
		else {
			$view->cosignEnabled = App::environment() === 'production' && Config::get("auth.cosignEnabled");
		}
		
		$formData['pass'] = ""; // never send the password back
		$view->form = $formData;
		$view->formErrors = $errors;
		$view->loggedIn = $loggedIn;
		$this->setContent($view, "login", "login");
	}
}
