<?php namespace uk\co\la1tv\website\models;

class MediaItemVideo extends MyEloquent {

	protected $table = 'media_items_video';
	protected $fillable = array('is_live_recording', 'time_recorded', 'scheduled_publish_time', 'enabled', 'name', 'description');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'media_items_video_id');
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('time_recorded', 'scheduled_publish_time'));
	}
	
	// returns true if this video should be accessible now. I.e mediaitem enabled and this enabled and scheduled_publish_time passed etc
	public function getIsAccessible() {
		return $this->mediaItem->getIsAccessible() && $this->enabled && $this->scheduled_publish_time->getTimestamp() >= time();
	}
	
}