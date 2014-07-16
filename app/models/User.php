<?php namespace uk\co\la1tv\website\models;

class User extends MyEloquent {

	protected $table = 'users';
	protected $fillable = array('cosign_user', 'username', 'password_hash', 'admin', 'disabled');
	
	public function permissionGroups() {
		return $this->belongsToMany(self::$p.'PermissionGroup', 'user_to_group', 'user_id', 'group_id');
	}

	public function getDates() {
		return array_merge(parent::getDates(), array('last_login_attempt'));
	}
}