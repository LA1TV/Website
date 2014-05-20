<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class PermissionGroup extends Eloquent {

	protected $table = 'permission_groups';
	protected $fillable = array('name', 'description');

}