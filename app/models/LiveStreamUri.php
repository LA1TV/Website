<?php namespace uk\co\la1tv\website\models;

class LiveStreamUri extends MyEloquent {
	
	protected $table = 'live_stream_uris';
	protected $fillable = array('uri', 'type', 'supported_devices');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array(array("qualityDefinition", "name")), $value);
	}
}