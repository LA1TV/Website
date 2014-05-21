<?php namespace uk\co\la1tv\website\models;

class MediaItemLiveStream extends MyEloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('enabled', 'scheduled_live_time');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
}