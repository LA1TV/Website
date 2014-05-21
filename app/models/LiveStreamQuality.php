<?php namespace uk\co\la1tv\website\models;

class LiveStreamQuality extends MyEloquent {
	
	protected $table = 'live_streams_qualities';
	protected $fillable = array('quality_id', 'name', 'position');

	public function liveStream() {
		return $this->belongsTo('LiveStream', 'live_stream_id');
	}
}	