<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class User extends Eloquent {

	protected $table = 'users';
	protected $fillable = array('cosign_user', 'admin');

}