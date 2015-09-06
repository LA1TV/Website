<?php namespace uk\co\la1tv\website\models;

use Exception;

class LiveStreamUri extends MyEloquent {
	
	protected $table = 'live_stream_uris';
	protected $fillable = array('uri', 'dvr_bridge_service_uri', 'has_dvr', 'type', 'supported_devices', 'enabled');

	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->dvr_bridge_service_uri && !is_null($model->has_dvr)) {
				throw new Exception("has_dvr must be null if it's a dvr bridge service uri.");
			}
			else if (!$model->dvr_bridge_service_uri && is_null($model->has_dvr)) {
				throw new Exception("has_dvr must not be null if it's not a dvr bridge service uri.");
			}
			return true;
		});
	}

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