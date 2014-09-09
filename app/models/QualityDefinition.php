<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;

class QualityDefinition extends MyEloquent {

	protected $table = 'quality_definitions';
	protected $fillable = array('id', 'name', 'position');
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'quality_definition_id');
	}
	
	public function liveStreamUris() {
		return $this->hasMany(self::$p.'LiveStreamUri', 'quality_definition_id');
	}
	
	public function liveStreams() {
		return $this->belongsToMany(self::$p.'LiveStream', 'quality_definition_to_live_stream', 'quality_definition_id', 'live_stream_id');
	}
	
	public static function isValidIdsFromAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new QualityDefinition();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new QualityDefinition();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new QualityDefinition();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->getStringForInput();
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name"), $value);
	}
}