<?php namespace uk\co\la1tv\website\models;

use Exception;

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
	public function resultsInNoAccessibleAdminLogin() {
		if ($this->admin && $this->isAccessible(true)) {
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
	
	// should be the string from the input
	public static function generateGroupsForOrderableList($stringFromInput) {
		$data = json_decode($stringFromInput, true);
		if (!is_array($data)) {
			return "[]";
		}
		$output = array();
		$ids = array();
		foreach($data as $a) {
			if (is_int($a) && !in_array($a, $ids, true)) {
				$ids[] = $a;
			}
			$output[] = array(
				"id"	=> is_int($a) ? $a : null,
				"text"	=> null
			);
		}
		if (count($ids) > 0) {
			$groups = PermissionGroup::whereIn("id", $ids)->get();
			$groupIds = array();
			foreach($groups as $a) {
				$groupIds[] = intval($a->id);
			}
			foreach($output as $i=>$a) {
				if (is_null($a['id'])) {
					continue;
				}
				$groupIndex = array_search($a['id'], $groupIds, true);
				if ($groupIndex === false) {
					$output[$i]["id"] = null; // if the quality can't be found anymore make the id null as well.
					continue;
				}
				$output[$i]["text"] = $groups[$groupIndex]->getNameAndDescription();
			}
		}
		return json_encode($output);
	}
	
	public static function isValidGroupsFromInput($stringFromInput) {
		$data = json_decode($stringFromInput, true);
		if (!is_array($data)) {
			return false;
		}
		$ids = array();
		foreach($data as $a) {
			if (!is_int($a) && !is_null($a)) {
				return false;
			}
			if (in_array($a, $ids, true)) {
				return false;
			}
			else {
				$ids[] = $a;
			}
		}
		if (count($ids) === 0) {
			return true;
		}
		return PermissionGroup::whereIn("id", $ids)->count() === count($ids);
	}
	
	public function getGroupsForInputAttribute() {
		$ids = array();
		foreach($this->getGroups() as $a) {
			$ids[] = $a['id'];
		}
		return json_encode($ids);
	}
	
	public function getGroupsForOrderableListAttribute() {
		$data = array();
		foreach($this->getGroups() as $a) {
			$data[] = array(
				"id"		=> $a['id'],
				"text"		=> $a['name']
			);
		}
		return json_encode($data);
	}
	
	public function getGroups() {
		$data = array();
		$items = $this->permissionGroups()->orderBy("position", "asc")->get();
		foreach($items as $a) {
			$data[] = array(
				"id"		=> intval($a->id),
				"name"		=> $a->getNameAndDescription()
			);
		}
		return $data;
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('last_login_attempt'));
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("cosign_user", "username"), $value);
	}
}