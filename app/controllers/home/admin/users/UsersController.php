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
use Auth;
use Response;
use uk\co\la1tv\website\models\User;
use uk\co\la1tv\website\models\PermissionGroup;

class UsersController extends UsersBaseController {

	public function getIndex() {
		$view = View::make('home.admin.users.index');
		$tableData = array();
		
		$pageNo = FormHelpers::getPageNo();
		$searchTerm = FormHelpers::getValue("search", "", false, true);
		
		// get shared lock on records so that they can't be deleted before query runs to get specific range
		// (this doesn't prevent new ones getting added but that doesn't really matter too much)
		$noUsers = User::search($searchTerm)->sharedLock()->count();
		$noPages = FormHelpers::getNoPages($noUsers);
		if ($pageNo > 0 && FormHelpers::getPageStartIndex() > $noUsers-1) {
			App::abort(404);
			return;
		}
		
		$users = User::with("permissionGroups")->search($searchTerm)->usePagination()->orderBy("disabled", "asc")->orderBy("admin", "desc")->orderBy("cosign_user", "asc")->orderBy("username", "asc")->orderBy("created_at", "desc")->sharedLock()->get();
		
		foreach($users as $a) {
			$enabled = !((boolean) $a->disabled);
			$enabledStr = $enabled ? "Yes" : "No";
			
			$admin = (boolean) $a->admin;
			$adminStr = $admin ? "Yes" : "No";

			$groupsStr = null;
			$groups = array();
			$groupModels = $a->permissionGroups()->orderBy("position", "asc")->get();
			if (count($groupModels) > 0) {
				foreach($groupModels as $b) {
					$groups[] = $b->name;
				}
				$groupsStr = implode(", ", $groups);
			}
			else {
				$groupsStr = "[No Groups]";
			}
			
			$tableData[] = array(
				"enabled"		=> $enabledStr,
				"enabledCss"	=> $enabled ? "text-success" : "text-danger",
				"admin"			=> $adminStr,
				"adminCss"		=> $admin ? "text-success" : "text-danger",
				"cosignUser"	=> !is_null($a->cosign_user) ? $a->cosign_user : "[No Cosign User]",
				"user"			=> !is_null($a->username) ? $a->username : "[No User]",
				"groups"		=> $groupsStr,
				"timeCreated"	=> $a->created_at->toDateTimeString(),
				"editUri"		=> Config::get("custom.admin_base_url") . "/users/edit/" . $a->id,
				"id"			=> $a->id
			);
		}
		$view->tableData = $tableData;
		$view->pageNo = $pageNo;
		$view->noPages = $noPages;
		$view->createUri = Config::get("custom.admin_base_url") . "/users/edit";
		$view->deleteUri = Config::get("custom.admin_base_url") . "/users/delete";
		$this->setContent($view, "users", "users");
	}
	
	public function anyEdit($id=null) {
		
		$user = null;
		$editing = false;
		if (!is_null($id)) {
			$user = User::with("permissionGroups")->find($id);
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
			array("cosign-user", ObjectHelpers::getProp("", $user, "cosign_user")),
			array("user", ObjectHelpers::getProp("", $user, "username")),
			array("password", ""),
			array("password-changed", "0"),
			array("groups", ObjectHelpers::getProp("[]", $user, "groups_for_input")),
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
		
		if (!$formSubmitted) {
			$additionalFormData['groupsInitialData'] = ObjectHelpers::getProp("[]", $user, "groups_for_orderable_list");
		}
		else {
			$additionalFormData['groupsInitialData'] = User::generateGroupsForOrderableList($formData['groups']);
		}
		
		$errors = null;
		
		if ($formSubmitted) {
		
			$modelCreated = DB::transaction(function() use (&$formData, &$user, &$errors) {
				
				Validator::extend('valid_password_changed_val', function($attribute, $value, $parameters) {
					return $value === "0" || $value === "1";
				});
				
				Validator::extend('unique_user', function($attribute, $value, $parameters) use (&$user) {
					$currentId = !is_null($user) ? intval($user->id) : null;
					$q = User::where("username", $value);
					if (!is_null($currentId)) {
						$q = $q->where("id", "!=", $currentId);
					}
					return $q->count() === 0;
				});
				
				Validator::extend('unique_cosign_user', function($attribute, $value, $parameters) use (&$user) {
					$currentId = !is_null($user) ? intval($user->id) : null;
					$q = User::where("cosign_user", $value);
					if (!is_null($currentId)) {
						$q = $q->where("id", "!=", $currentId);
					}
					return $q->count() === 0;
				});
				
				Validator::extend('valid_groups', function($attribute, $value, $parameters) {
					return User::isValidGroupsFromInput($value);
				});
				
				$validator = Validator::make($formData, array(
					'password-changed'	=> array('required', 'valid_password_changed_val'),
					'cosign-user'	=> array('max:32', 'unique_cosign_user'),
					'user'			=> array('required_with:password', 'alpha_dash', 'unique_user'),
					'groups'		=> array('required', 'valid_groups')
				), array(
					'password-changed.required'	=> "",
					'password-changed.valid_password_changed_val'	=> "",
					'cosign-user.max'			=> FormHelpers::getLessThanCharactersMsg(32),
					'cosign-user.unique_cosign_user'	=> "There is already another account associated with this username.",
					'user.required_with'		=> FormHelpers::getRequiredMsg(),
					'user.required'				=> FormHelpers::getRequiredMsg(),
					'user.unique_user'			=> "An account with this username already exists.",
					'user.alpha_dash'			=> FormHelpers::getInvalidAlphaDashMsg(),
					'password.required'			=> FormHelpers::getRequiredMsg(),
					'groups.required'			=> FormHelpers::getGenericInvalidMsg(),
					'groups.valid_groups'		=> FormHelpers::getGenericInvalidMsg()
				));
				
				// if user has not chosen to change password, but left user empty, this is not allowed.
				// user can only be empty when there is no password set.
				$validator->sometimes("user", "required", function($input) use (&$formData) {
					return $formData['password-changed'] === "0";
				});
				
				$validator->sometimes("password", "required", function($input) use (&$user, &$formData) {
					return !empty($formData['user']) && $formData['password-changed'] === "1";
				});
				
				if (!$validator->fails()) {
					// everything is good. save model
					// build the model now. Then validate that there is at least one admin. Done in this order so that resultsInNoAccessibleAdminLogin() works.
					
					if (is_null($user)) {
						$user = new User();
					}
					
					$user->disabled = !FormHelpers::toBoolean($formData['enabled']);
					$user->admin = FormHelpers::toBoolean($formData['admin']);
					$user->cosign_user = FormHelpers::nullIfEmpty($formData['cosign-user']);
					$username = FormHelpers::nullIfEmpty($formData['user']);
					$user->username = $username;
					if (!is_null($username)) {
						if ($formData['password-changed'] === "1") {
							$password = FormHelpers::nullIfEmpty($formData['password']);
							$user->password_hash = !is_null($password) ? Hash::make($password) : null;
						}
					}
					else {
						$user->password_hash = null;
					}
					
					
					// validate that there is at least one admin with access.
					$validator = Validator::make($formData, array(
					), array(
						'admin.required'			=> "This user must be admin otherwise there is no admin on the system with access.",
						'user.required'				=> "A user is required because there must be at least one admin in the system with a username and password.",
						'password.required'			=> "A password is required because there must be at least one admin in the system with a username and password."
					));
					
					$validator->sometimes(array("admin", "user"), "required", function($input) use (&$user) {
						return $user->resultsInNoAccessibleAdminLogin();
					});
					
					$validator->sometimes("password", "required", function($input) use (&$user, &$formData) {
						return $user->resultsInNoAccessibleAdminLogin() && $formData['password-changed'] === "1";
					});
					
					if (!$validator->fails()) {
						if ($user->save() === false) {
							throw(new Exception("Error saving User."));
						}
						
						$user->permissionGroups()->detach(); // detaches all
						$ids = json_decode($formData['groups'], true);
						if (count($ids) > 0) {
							$groups = PermissionGroup::whereIn("id", $ids)->get();
							foreach($groups as $a) {
								$user->permissionGroups()->attach($a);
							}
						}
							
						// the transaction callback result is returned out of the transaction function
						return true;
					}
					else {
						$errors = $validator->messages();
						return false;
					}
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
	
	public function handleDelete() {
		$resp = array("success"=>false);
		if (Csrf::hasValidToken() && Auth::isLoggedIn() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			DB::transaction(function() use (&$id, &$resp) {
				$user = User::find($id);
				if (!is_null($user)) {
					if (!$user->resultsInNoAccessibleAdminLogin(true)) {
						if ($user->delete() === false) {
							throw(new Exception("Error deleting User."));
						}
						$resp['success'] = true;
					}
					else {
						$resp['msg'] = "This user cannot be deleted at the moment as it would result in no admin with access to the system.";
					}
				}
			});
		}
		return Response::json($resp);
	}
}
