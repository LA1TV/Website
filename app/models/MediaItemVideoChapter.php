<?php namespace uk\co\la1tv\website\models;

class MediaItemVideoChapter extends MyEloquent {

	protected $table = 'media_items_video_chapters';
	protected $fillable = array('id', 'title', 'time');
	protected $appends = array("time_str");

	public function mediaItemVideo() {
		return $this->belongsTo(self::$p.'MediaItemVideo', 'media_item_video_id');
	}
	
	public function getTimeStrAttribute() {
		$hours = floor($this->time / (60*60));
		$minutes = floor($this->time / 60) % 60;
		$seconds = $this->time % 60;
		$str = "";
		if ($hours > 0) {
			$str = $hours."h ";
		}
		$str .= $minutes."m ".$seconds."s";
		return $str;
	}
}