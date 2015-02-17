<?php namespace uk\co\la1tv\website\models;

class MediaItemVideoChapter extends MyEloquent {

	protected $table = 'media_items_video_chapters';
	protected $fillable = array('id', 'title', 'time');

	public function mediaItemVideo() {
		return $this->belongsTo(self::$p.'MediaItemVideo', 'media_item_video_id');
	}
}