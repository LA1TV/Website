<?php namespace uk\co\la1tv\website\models;

class MediaItemVideoChapter extends MyEloquent {

	protected $table = 'media_items_video_chapters';
	protected $fillable = array('id', 'title', 'time');
	protected $appends = array("time_str");

	public function mediaItemVideo() {
		return $this->belongsTo(self::$p.'MediaItemVideo', 'media_item_video_id');
	}
	
	public function getTimeStrAttribute() {
		$minutes = floor($this->time / 60);
		$seconds = $this->time % 60;
		return $minutes."m ".$seconds."s";
	}
}