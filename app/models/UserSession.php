<?php namespace uk\co\la1tv\website\models;

use Exception;
use App;

class UserSession extends MyEloquent {

	protected $table = 'users_sessions';
	protected $fillable = array('user_id', 'session_id');
	
	public function user() {
		return $this->belongsTo(self::$p.'User', 'user_id');
	}
}