<?php namespace uk\co\la1tv\website\models;

class LiveStreamWatchingNow extends MyEloquent {
	
	protected $table = 'live_streams_watching_now';
	protected $fillable = array('session_id');

	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
}