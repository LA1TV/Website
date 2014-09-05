<?php namespace uk\co\la1tv\website\models;

use DB;
use Session;
use Carbon;
use Config;

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
	
	public function registerViewCount() {
		
		if (!$this->getIsAccessible()) {
			return;
		}
	
		$sessionKey = "viewCount-".$this->id;
		$lastTimeRegistered = Session::get($sessionKey, null);
		if (!is_null($lastTimeRegistered) && $lastTimeRegistered >= Carbon::now()->subMinutes(Config::get("custom.interval_between_registering_view_counts"))->timestamp) {
			// already registered view not that long ago.
			return;
		}
		
		DB::transaction(function() {
			$this->view_count++;
			$this->save();
		});
		Session::set($sessionKey, Carbon::now()->timestamp);
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