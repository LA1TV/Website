<?php namespace uk\co\la1tv\website\models;

use Exception;
use App;

class User extends MyEloquent {

	protected $table = 'users';
	protected $fillable = array('cosign_user', 'username', 'password_hash', 'admin', 'disabled');
	protected $appends = array("groups_for_input", "groups_for_ordeable_list");

	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->resultsInNoAccessibleAdminLogin()) {
				throw(new Exception("Cannot save this user as it would result in there being no admin with access to the system."));
			}
			return true;
		});
		
		self::deleting(function($model) {
			if ($model->resultsInNoAccessibleAdminLogin(true)) {
				throw(new Exception("Cannot delete this user as it would result in there being no admin with access to the system."));
			}
			return true;
		});
	}

	public function userSessions() {
		return $this->hasMany(self::$p.'UserSession', 'user_id');
	}
	
	public function permissionGroups() {
		return $this->belongsToMany(self::$p.'PermissionGroup', 'user_to_group', 'user_id', 'group_id');
	}
	
	// $password should be null if there is one set, but is unknown
	public static function generateContentForPasswordToggleableComponent($password) {
		$componentState = is_null($password) ? null : array(
			"value"	=> $password
		);
		return json_encode(array(
			"componentToggled"	=> !is_null($password),
			"componentState"	=> $componentState
		));
	}
	
	// returns true if saving the current model would result in no accessible admin.
	// Must be an admin that can login with a username and password. Don't want to end up relying on just cosign.
	// if $ignoreCurrentUser is TRUE the current user will not be taken into consideration.
	public function resultsInNoAccessibleAdminLogin($ignoreCurrentUser=false) {
		if (!$ignoreCurrentUser && $this->admin && $this->isAccessible(true)) {
			return false;
		}
		$q = self::where("admin", true)->accessible(true);
		if ($this->exists) {
			$q = $q->where("id", "!=", $this->id);
		}
		return $q->count() === 0;
	}
	
	// scopes query to only return accounts that have a method of logging in.
	// when $ignoreCosign is true this will not be considered as a valid method of login
	public function scopeAccessible($q, $ignoreCosign=false) {
        return $q->where("disabled", false)->where(function($q) use (&$ignoreCosign) {
			$q->whereNotNull("username");
			if (!$ignoreCosign) {
				$q->orWhereNotNull("cosign_user");
			}
		});
    }
	
	public function isAccessible($ignoreCosign=false) {
		return !$this->disabled && (!is_null($this->username) || (!$ignoreCosign && !is_null($this->cosign_user)));
	}

	private function getGroupsIdsForReorderableList() {
		$ids = array();
		$items = $this->permissionGroups()->orderBy("position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getGroupsForInputAttribute() {
		return PermissionGroup::generateInputValueForAjaxSelectOrderableList($this->getGroupsIdsForReorderableList());
	}
	
	public function getGroupsForOrderableListAttribute() {
		return PermissionGroup::generateInitialDataForAjaxSelectOrderableList($this->getGroupsIdsForReorderableList());
	}
	
	public function hasPermission($permissionId, $permissionFlag=0) {
		// if the user is an admin then they will always have permission
		if ($this->admin) {
			return true;
		}
		
		$this->load("permissionGroups", "permissionGroups.permissions");
		$permission = null;
		foreach($this->permissionGroups as $group) {
			$currentPermission = $group->permissions->find($permissionId);
			if (!is_null($currentPermission)) {
				if (is_null($permission) || intval($permission->pivot->permission_flag) < intval($currentPermission->pivot->permission_flag)) {
					$permission = $currentPermission;
				}
			}
		}
		return !is_null($permission) && intval($permissionFlag) <= intval($permission->pivot->permission_flag);
	}
	
	public function hasPermissionOr401($permissionId, $permissionFlag=0) {
		if (!$this->hasPermission($permissionId, $permissionFlag)) {
			// changed this to a 403 because a 401 was causing the browsers log in dialog to be shown
			App::abort(403); // unauthorized
			return;
		}
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('last_login_attempt'));
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("cosign_user", "username"), $value);
	}
}