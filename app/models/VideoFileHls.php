<?php namespace uk\co\la1tv\website\models;

class VideoFileHls extends MyEloquent {

	protected $table = 'video_files_hls';
	protected $guarded = array('*');
	
	public function playlistFile() {
		return $this->belongsTo(self::$p.'File', 'playlist_file_id');
	}

	public function segmentFile() {
		return $this->belongsTo(self::$p.'File', 'segment_file_id');
	}
	
	public function videoFile() {
		return $this->belongsTo(self::$p.'VideoFile', 'video_files_id');
	}

}