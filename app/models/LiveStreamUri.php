<?php namespace uk\co\la1tv\website\models;

class LiveStreamUri extends MyEloquent {
	
	protected $table = 'live_stream_uris';
	protected $fillable = array('uri_template', 'type', 'supported_devices');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
	// the $domain can also be an ip address
	public function getBuiltUrl($domain, $appName, $streamName) {
		$url = $this->uri_template;
		$url = str_replace("{domain}", $domain, $url);
		$url = str_replace("{appName}", $appName, $url);
		$url = str_replace("{streamName}", $streamName, $url);
		return $url;
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array(array("qualityDefinition", "name")), $value);
	}
}