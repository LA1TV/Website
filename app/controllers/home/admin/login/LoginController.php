<?php namespace uk\co\la1tv\website\controllers\home\admin\login;

use View;
use FormHelpers;
use Csrf;
use Validator;
use Auth;

class LoginController extends LoginBaseController {

	public function anyIndex() {
		
		$formSubmitted = isset($_POST['form-submitted']);
	
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
		
		if ($formSubmitted) {
		
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
			
			if (!$validator->fails()) {
				
			}
			else {
				$errors = $validator->messages();
			}
		}
		
		$formData['pass'] = ""; // never send the password back
		
		$view = View::make('home.admin.login.index');
		$view->form = $formData;
		$view->formErrors = $errors;
	
		$this->setContent($view, "login", "login");
	}
}
