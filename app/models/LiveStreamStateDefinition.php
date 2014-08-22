<?php namespace uk\co\la1tv\website\models;

class LiveStreamStateDefinition extends MyEloquent {

	protected $table = 'live_stream_state_definitions';
	protected $fillable = array('id', 'name');
	
	public function liveStreams() {
		return $this->hasMany(self::$p.'MediaItemLiveStream', 'state_id');
	}
}