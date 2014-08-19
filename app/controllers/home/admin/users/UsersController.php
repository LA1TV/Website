<?php namespace uk\co\la1tv\website\controllers\home\admin\users;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
use Csrf;
use DB;
use Validator;
use Redirect;
use Hash;
use uk\co\la1tv\website\models\User;

class UsersController extends UsersBaseController {

	public function getIndex() {
		$this->setContent(View::make('home.admin.users.index'), "users", "users");
	}
	
	public function anyEdit($id=null) {
		
		$user = null;
		$editing = false;
		if (!is_null($id)) {
			$user = User::find($id);
			if (is_null($user)) {
				App::abort(404);
				return;
			}
			$editing = true;
		}
		
		$formSubmitted = isset($_POST['form-submitted']) && $_POST['form-submitted'] === "1"; // has id 1
	
		if ($formSubmitted) {
			// throws exception if token invalid
			Csrf::check();
		};
		
		// populate $formData with default values or received values
		$formData = FormHelpers::getFormData(array(
			array("enabled", ObjectHelpers::getProp(true, $user, "enabled")?"y":""),
			array("admin", ObjectHelpers::getProp(false, $user, "admin")?"y":""),
			array("cosign-user", ObjectHelpers::getProp("", $user, "cosign-user")),
			array("user", ObjectHelpers::getProp("", $user, "username")),
			array("password", ""),
			array("password-changed", "0"),
			array("groups", "")
		), !$formSubmitted);
		
		$passwordToDisplay = null;
		if ($formData['password-changed'] === "1") {
			$passwordToDisplay = $formData['password'];
		}
		else {
			$passwordToDisplay = is_null(ObjectHelpers::getProp(null, $user, "password_hash")) ? "" : null;
		}
		
		$additionalFormData = array(
			"passwordInitialData"	=> User::generateContentForPasswordToggleableComponent($passwordToDisplay),
			"passwordToggleEnabled"	=> !is_null(ObjectHelpers::getProp(null, $user, "password_hash")),
			"passwordChanged"		=> !is_null($passwordToDisplay),
			"groupsInitialData"		=> null
		);
		
		$errors = null;
		
		if ($formSubmitted) {
		
			$modelCreated = DB::transaction(function() use (&$formData, &$user, &$errors) {
				
				Validator::extend('valid_password_changed_val', function($attribute, $value, $parameters) {
					return $value === "0" || $value === "1";
				});
				
				Validator::extend('unique_user', function($attribute, $value, $parameters) {
					return User::where("username", $value)->count() === 0;
				});
				
				Validator::extend('unique_cosign_user', function($attribute, $value, $parameters) {
					return User::where("cosign_user", $value)->count() === 0;
				});
				
				$validator = Validator::make($formData, array(
					'password-changed'	=> array('required', 'valid_password_changed_val'),
					'cosign-user'	=> array('max:32', 'unique_cosign_user'),
					'user'			=> array('required_with:password', 'alpha_dash', 'unique_user')
				), array(
					'password-changed.required'	=> "",
					'password-changed.valid_password_changed_val'	=> "",
					'cosign-user.max'			=> FormHelpers::getLessThanCharactersMsg(32),
					'user.required_with'		=> FormHelpers::getRequiredMsg(),
					'user.required'				=> FormHelpers::getRequiredMsg(),
					'user.unique_user'			=> "An account with this username already exists.",
					'user.alpha_dash'			=> FormHelpers::getInvalidAlphaDashMsg(),
					'user.unique_cosign_user'	=> "There is already another account associated with this username.",
					'password.required'			=> FormHelpers::getRequiredMsg()
				));
				
				// if user has not chosen to change password, but left user empty, this is not allowed.
				// user can only be empty when there is no password set.
				$validator->sometimes("user", "required", function($input) use (&$formData) {
					return $formData['password-changed'] === "0" && empty($formData['user']);
				});
				
				$validator->sometimes("password", "required", function($input) use (&$formData) {
					return !empty($formData['user']) && $formData['password-changed'] === "1" && $formData['password'] === "";
				});
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($user)) {
						$user = new User();
					}
					
					$user->disabled = !FormHelpers::toBoolean($formData['enabled']);
					$user->admin = FormHelpers::toBoolean($formData['admin']);
					$user->cosign_user = FormHelpers::nullIfEmpty($formData['cosign-user']);
					$username = FormHelpers::nullIfEmpty($formData['user']);
					$user->username = $username;
					if (!is_null($username)) {
						$password = FormHelpers::nullIfEmpty($formData['password']);
						$user->password_hash = !is_null($password) ? Hash::make($password) : null;
					}
					else {
						$user->password_hash = null;
					}
					
					if ($user->save() === false) {
						throw(new Exception("Error saving User."));
					}
					
					// the transaction callback result is returned out of the transaction function
					return true;
				}
				else {
					$errors = $validator->messages();
					return false;
				}
			});
			
			if ($modelCreated) {
				return Redirect::to(Config::get("custom.admin_base_url") . "/users");
			}
			// if not valid then return form again with errors
		}
		
		$view = View::make('home.admin.users.edit');
		$view->editing = $editing;
		$view->form = $formData;
		$view->additionalForm = $additionalFormData;
		$view->formErrors = $errors;
		$view->cancelUri = Config::get("custom.admin_base_url") . "/users";
	
		$this->setContent($view, "users", "users-edit");
	}
	
	public function postDelete() {
		// TODO
		return;
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$series = Series::find($id);
				if (!is_null($series)) {
					if ($series->delete() === false) {
						throw(new Exception("Error deleting Series."));
					}
					$resp['success'] = true;
				}
			});
		}
		return Response::json($resp);
	}
}
