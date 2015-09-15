<?php namespace uk\co\la1tv\website\models;

class VideoFileDash extends MyEloquent {

	protected $table = 'video_files_dash';
	protected $guarded = array('*');
	
	public function mediaPresentationDescriptionFile() {
		return $this->belongsTo(self::$p.'File', 'media_presentation_description_file_id');
	}

	public function audioChannelFile() {
		return $this->belongsTo(self::$p.'File', 'audio_channel_file_id');
	}

	public function videoChannelFile() {
		return $this->belongsTo(self::$p.'File', 'video_channel_file_id');
	}
	
	public function videoFile() {
		return $this->belongsTo(self::$p.'VideoFile', 'video_files_id');
	}

}