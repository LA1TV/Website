<?php namespace uk\co\la1tv\website\models;

class User extends MyEloquent {

	protected $table = 'users';
	protected $fillable = array('cosign_user', 'username', 'password_hash', 'admin', 'disabled');
	
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
	
	public function getDates() {
		return array_merge(parent::getDates(), array('last_login_attempt'));
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("cosign_user", "username"), $value);
	}
}