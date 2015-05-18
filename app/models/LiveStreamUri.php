<?php namespace uk\co\la1tv\website\models;

class LiveStreamUri extends MyEloquent {
	
	protected $table = 'live_stream_uris';
	protected $fillable = array('uri', 'dvr_bridge_service_uri', 'type', 'supported_devices', 'enabled');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function dvrLiveStreamUris() {
		return $this->hasMany(self::$p.'DvrLiveStreamUri', 'live_stream_uri_id');
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array(array("qualityDefinition", "name")), $value);
	}
}