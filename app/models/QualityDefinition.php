<?php namespace uk\co\la1tv\website\models;

class QualityDefinition extends MyEloquent {

	protected $table = 'quality_definitions';
	protected $fillable = array('id', 'name', 'position');
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'quality_definition_id');
	}
	
	public function liveStreamQualities() {
		return $this->hasMany(self::$p.'LiveStreamQuality', 'quality_definition_id');
	}
	
}