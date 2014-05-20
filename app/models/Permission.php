<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class Permission extends Eloquent {

	protected $table = 'permissions';
	protected $fillable = array('description');

}