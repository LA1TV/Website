<?php namespace uk\co\la1tv\website\models;

use FormHelpers;

class MediaItemVideo extends MyEloquent {

	protected $table = 'media_items_video';
	protected $fillable = array('is_live_recording', 'time_recorded', 'scheduled_publish_time', 'enabled', 'name', 'description');
	protected $appends = array("time_recorded_for_input", "scheduled_publish_time_for_input");
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function coverArtFile() {
		return $this->belongsTo(self::$p.'File', 'cover_art_file_id');
	}
	
	public function getTimeRecordedForInputAttribute() {
		if (is_null($this->time_recorded)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->time_recorded->timestamp);
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('time_recorded', 'scheduled_publish_time'));
	}
	
	// returns true if this video should be accessible now. I.e mediaitem enabled and this enabled and scheduled_publish_time passed etc
	public function getIsAccessible() {
		$scheduledPublishTime = $this->scheduled_publish_time;
		return $this->mediaItem->getIsAccessible() && $this->enabled && (is_null($scheduledPublishTime) || $scheduledPublishTime->isPast()) && $this->sourceFile->getFinishedProcessing();
	}
	
}