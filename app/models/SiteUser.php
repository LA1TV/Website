<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class SiteUser extends Eloquent {

	protected $table = 'site_users';
	protected $fillable = array('fb_uid', 'first_name', 'last_name', 'name', 'email');
	
	public function comments() {
		return $this->hasMany('MediaItemComment', 'site_user_id');
	}

	public function likes() {
		return $this->hasMany('MediaItemLike', 'site_user_id');
	}

}