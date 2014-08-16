<?php namespace uk\co\la1tv\website\models;

use FormHelpers;

class MediaItemLiveStream extends MyEloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('name', 'description', 'enabled', 'scheduled_live_time');
	protected $appends = array("scheduled_live_time_for_input");
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function coverArtFile() {
		return $this->belongsTo(self::$p.'File', 'cover_art_file_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function getScheduledLiveTimeForInputAttribute() {
		if (is_null($this->scheduled_live_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_live_time->timestamp);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('scheduled_live_time'));
	}
	
	public function getIsAccessible() {
		$liveTime = $this->scheduled_live_time;
		return $this->mediaItem->getIsAccessible() && $this->enabled && $this->liveStream->enabled && (is_null($liveTime) || $liveTime->isPast());
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}