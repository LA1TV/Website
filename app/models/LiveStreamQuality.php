<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;

class LiveStreamQuality extends MyEloquent {
	
	protected $table = 'live_streams_qualities';
	protected $fillable = array('id', 'uri_template', 'position');

	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
	public function liveStreams() {
		return $this->belongsToMany(self::$p.'LiveStream', 'live_stream_qualitiy_to_live_stream', 'live_stream_quality_id', 'live_stream_id');
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
	
	public static function isValidIdsFromAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new LiveStreamQuality();
		}, function($model) {
			return $model->qualityDefinition->name;
		});
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new LiveStreamQuality();
		}, function($model) {
			return $model->qualityDefinition->name;
		});
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new LiveStreamQuality();
		}, function($model) {
			return $model->qualityDefinition->name;
		});
		return $reorderableList->getStringForInput();
	}
}