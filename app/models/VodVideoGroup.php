<?php namespace uk\co\la1tv\website\models;

class QualityDefinition extends MyEloquent {

	protected $table = 'vod_video_groups';
	protected $fillable = array();
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'vod_video_group_id');
	}
	
}