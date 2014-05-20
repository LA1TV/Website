<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItemVideo extends Eloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('width', 'height', 'is_live_recording', 'time_recorded', 'scheduled_publish_time', 'enabled', 'name', 'description');
	
	public function mediaItem() {
		return $this->belongsTo('MediaItem', 'media_item_id');
	}
}