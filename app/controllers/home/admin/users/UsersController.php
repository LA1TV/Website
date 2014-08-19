<?php namespace uk\co\la1tv\website\controllers\home\admin\users;

use View;
use FormHelpers;
use ObjectHelpers;
use Config;
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
			array("user", ObjectHelpers::getProp("", $user, "user")),
			array("password", ""),
			array("password-changed", "0"),
			array("groups", "")
		), !$formSubmitted);
		
		$passwordToDisplay = null;
		if ($formData['password-changed'] === "1") {
			$passwordToDisplay = $formData['password'];
		}
		else {
			$passwordToDisplay = is_null(ObjectHelpers::getProp(null, $user, "password")) ? "" : null;
		}
		
		$additionalFormData = array(
			"passwordInitialData"	=> User::generateContentForPasswordToggleableComponent($passwordToDisplay),
			"passwordToggleEnabled"	=> !is_null(ObjectHelpers::getProp(null, $user, "password")),
			"passwordChanged"		=> false,
			"groupsInitialData"		=> null
		);
		
		$errors = null;
		
		/*if ($formSubmitted) {
		
			$modelCreated = DB::transaction(function() use (&$formData, &$series, &$errors) {
			
				$validator = Validator::make($formData,	array(
					'name'		=> array('required', 'max:50'),
					'description'	=> array('max:500')
				), array(
					'name.required'		=> FormHelpers::getRequiredMsg(),
					'name.max'			=> FormHelpers::getLessThanCharactersMsg(50),
					'description.max'	=> FormHelpers::getLessThanCharactersMsg(500)
				));
				
				if (!$validator->fails()) {
					// everything is good. save/create model
					if (is_null($series)) {
						$series = new Series();
					}
					
					$series->name = $formData['name'];
					$series->description = FormHelpers::nullIfEmpty($formData['description']);
					$series->enabled = FormHelpers::toBoolean($formData['enabled']);
					
					if ($series->save() === false) {
						throw(new Exception("Error saving Series."));
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
				return Redirect::to(Config::get("custom.admin_base_url") . "/series");
			}
			// if not valid then return form again with errors
		}
		*/
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
