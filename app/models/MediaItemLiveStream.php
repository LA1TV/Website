<?php namespace uk\co\la1tv\website\models;

class MediaItemLiveStream extends MyEloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('name', 'description', 'enabled', 'state_id', 'information_msg', 'being_recorded');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function stateDefinition() {
		return $this->belongsTo(self::$p.'LiveStreamStateDefinition', 'state_id');
	}
	
	public function getIsAccessible() {
		$liveStream = $this->liveStream;
		return $this->mediaItem->getIsAccessible() && $this->enabled && !is_null($liveStream) && $liveStream->getIsAccessible();
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("liveStream", function($q2) {
			$q2->accessible();
		});
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}