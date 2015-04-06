<?php namespace uk\co\la1tv\website\models;

class VideoScrubThumbnailFile extends MyEloquent {

	protected $table = 'video_scrub_thumbnail_files';
	protected $fillable = array('time');
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}

}