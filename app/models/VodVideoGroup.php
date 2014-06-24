<?php namespace uk\co\la1tv\website\models;

class VodVideoGroup extends MyEloquent {

	protected $table = 'vod_video_groups';
	protected $fillable = array();
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'vod_video_group_id');
	}
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
}