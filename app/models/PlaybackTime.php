<?php namespace uk\co\la1tv\website\models;

class PlaybackTime extends MyEloquent {

	protected $table = 'playback_times';
	protected $fillable = array('time');
	
	public function user() {
		return $this->belongsTo(self::$p.'User', 'user_id');
	}
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
}