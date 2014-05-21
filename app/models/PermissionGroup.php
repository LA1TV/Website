<?php namespace uk\co\la1tv\website\models;

class PermissionGroup extends MyEloquent {

	protected $table = 'permission_groups';
	protected $fillable = array('name', 'description');

	public function users() {
		return $this->belongsToMany(self::$p.'User', 'user_to_group', 'group_id', 'user_id');
	}
	
	public function permissions() {
		return $this->belongsToMany(self::$p.'PermissionGroup', 'permission_to_group', 'group_id', 'permission_id')->withPivot('permission_flag');
	}
}