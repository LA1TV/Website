<?php namespace uk\co\la1tv\website\models;

class LiveStreamQuality extends MyEloquent {
	
	protected $table = 'live_streams_qualities';
	protected $fillable = array('id', 'uri_template', 'position');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'LiveStreamQuality', 'quality_definition_id');
	}
	
	// the $domain can also be an ip address
	public function getBuiltUrl($domain, $appName, $streamName) {
		$url = $this->uri_template;
		$url = str_replace("{domain}", $domain, $url);
		$url = str_replace("{appName}", $appName, $url);
		$url = str_replace("{streamName}", $streamName, $url);
		return $url;
	}
}	