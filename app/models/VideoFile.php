<?php namespace uk\co\la1tv\website\models;

class VideoFile extends MyEloquent {

	protected $table = 'video_files';
	protected $fillable = array('width', 'height');
	
	public function mediaItemVideo() {
		return $this->belongsTo(self::$p.'MediaItemVideo', 'media_items_video_id');
	}
	
	public function qualityDefinition() {
		return $this->hasOne(self::$p.'QualityDefinition', 'quality_definition_id');
	}
	
}