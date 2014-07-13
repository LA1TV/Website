<?php namespace uk\co\la1tv\website\models;

class User extends MyEloquent {

	protected $table = 'users';
	protected $fillable = array('cosign_user', 'username', 'password_hash', 'admin');
	
	public function permissionGroups() {
		return $this->belongsToMany(self::$p.'PermissionGroup', 'user_to_group', 'user_id', 'group_id');
	}
}