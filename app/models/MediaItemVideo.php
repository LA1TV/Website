<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItemVideo extends Eloquent {

	protected $table = 'media_items_video';
	protected $fillable = array('is_live_recording', 'time_recorded', 'scheduled_publish_time', 'enabled', 'name', 'description');
	
	public function mediaItem() {
		return $this->belongsTo('MediaItem', 'media_item_id');
	}
}