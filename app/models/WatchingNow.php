<?php namespace uk\co\la1tv\website\models;

class WatchingNow extends MyEloquent {
	
	protected $table = 'watching_now';
	protected $fillable = array('session_id', 'playing', 'last_play_time');

	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}

	public function getDates() {
		return array_merge(parent::getDates(), array('last_play_time'));
	}
	
}