<?php namespace uk\co\la1tv\website\models;

class VideoFile extends MyEloquent {

	protected $table = 'video_files';
	protected $fillable = array('width', 'height');
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
	
	public function qualityDefinition() {
		return $this->belongsTo(self::$p.'QualityDefinition', 'quality_definition_id');
	}

	public function videoFileDash() {
		return $this->hasOne(self::$p.'VideoFileDash', 'video_files_id');
	}

	public function videoFileHls() {
		return $this->hasOne(self::$p.'VideoFileHls', 'video_files_id');
	}
}