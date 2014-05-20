<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class PermissionGroup extends Eloquent {

	protected $table = 'permission_groups';
	protected $fillable = array('name', 'description');

	public function users() {
		return $this->belongsToMany('User', 'user_to_group', 'group_id', 'user_id');
	}
	
	public function permissions() {
		return $this->belongsToMany('PermissionGroup', 'permission_to_group', 'group_id', 'permission_id')->withPivot('permission_flag');
	}
}