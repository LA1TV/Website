<?php namespace uk\co\la1tv\website\models;

class QualityDefinition extends MyEloquent {

	protected $table = 'quality_definitions';
	protected $fillable = array('id', 'name');
	
	public function videoFile() {
		return $this->hasOne(self::$p.'VideoFile', 'quality_definition_id');
	}
	
	public function liveStreamQuality() {
		return $this->hasOne(self::$p.'LiveStreamQuality', 'quality_definition_id');
	}
	
}