<?php namespace uk\co\la1tv\website\models;

class EmailTasksMediaItem extends MyEloquent {

	protected $table = 'email_tasks_media_item';
	protected $fillable = array('message_type_id');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
}