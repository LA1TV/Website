<?php namespace uk\co\la1tv\website\models;

class WatchingNow extends MyEloquent {
	
	protected $table = 'watching_now';
	protected $fillable = array('session_id');

	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
}