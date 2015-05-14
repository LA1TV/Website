<?php namespace uk\co\la1tv\website\models;

class DvrLiveStreamUri extends MyEloquent {
	
	protected $table = 'dvr_live_stream_uris';
	protected $fillable = array('uri');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
	public function liveStreamUri() {
		return $this->belongsTo(self::$p.'LiveStreamUri', 'live_stream_uri_id');
	}

	public function mediaItemLiveStream() {
		return $this->belongsTo(self::$p.'MediaItemLiveStream', 'media_item_live_stream_id');
	}
}