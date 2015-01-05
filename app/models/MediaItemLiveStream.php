<?php namespace uk\co\la1tv\website\models;

use \Session as SessionProvider;
use Carbon;
use Config;
use Queue;
use Event;

class MediaItemLiveStream extends MyEloquent {

	protected $table = 'media_items_live_stream';
	protected $fillable = array('enabled', 'state_id', 'information_msg', 'being_recorded', 'external_stream_url');
	
	protected static function boot() {
		parent::boot();
		self::saved(function($model) {
		
			if ($model->hasJustBecomeLive()) {
				
				// queue the email job once the response has been sent to the user just before the script ends
				// this makes sure if this is currently in a transaction the transaction will have ended when the job is queued
				Event::listen('app.finish', function() use (&$model) {
					Queue::push("uk\co\la1tv\website\jobs\MediaItemLiveEmailsJob", array("mediaItemId"=>intval($model->mediaItem->id)));
				});
			}
		});
	}
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function liveStream() {
		return $this->belongsTo(self::$p.'LiveStream', 'live_stream_id');
	}
	
	public function stateDefinition() {
		return $this->belongsTo(self::$p.'LiveStreamStateDefinition', 'state_id');
	}
	
	// if the state is set to "live" but there is no live stream attached to, or the attached live stream is not live, then the resolved version is "Not Live"
	public function getResolvedStateDefinition($stateDefinitionParam=null) {
		$stateDefinition = is_null($stateDefinitionParam) ? $this->stateDefinition : $stateDefinitionParam;
		if (intval($stateDefinition->id) === 2 && (is_null($this->liveStream) || !$this->liveStream->getIsAccessible())) {
			// set to "live" but no live stream attached or live. Pretend "Not Live"
			return LiveStreamStateDefinition::find(1);
		}
		return $stateDefinition;
	}
	
	public function registerViewCount() {	
		if (!$this->getIsAccessible() || intval($this->getResolvedStateDefinition()->id) !== 2) {
			// shouldn't be accessible or stream not live
			return;
		}
	
		$sessionKey = "viewCount-".$this->id;
		$lastTimeRegistered = SessionProvider::get($sessionKey, null);
		if (!is_null($lastTimeRegistered) && $lastTimeRegistered >= Carbon::now()->subMinutes(Config::get("custom.interval_between_registering_view_counts"))->timestamp) {
			// already registered view not that long ago.
			return;
		}
		$this->increment("view_count");
		SessionProvider::set($sessionKey, Carbon::now()->timestamp);
	}
	
	// returns true if this should be shown with the parent media item. If false then it should like the MediaItem does not have a live stream component.
	// this can still return true even if there is no LiveStream model associated with this.
	// getResolvedStateDefinition() should be used to determine the state of the actual stream.
	public function getIsAccessible() {
		return $this->enabled && $this->mediaItem->getIsAccessible();
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("mediaItem", function($q2) {
			$q2->accessible();
		});
	}
	
	public function isNotLive() {
		return intval($this->getResolvedStateDefinition()->id) === 1;
	}
	
	public function isLive($stateDefinition=null) {
		return intval($this->getResolvedStateDefinition($stateDefinition)->id) === 2;
	}
	
	public function isOver() {
		return intval($this->getResolvedStateDefinition()->id) === 3;
	}
	
	// not live when has state_id of 1 or when has live state id but the live stream is nonexistent or inaccessible
	public function scopeNotLive($q, $yes=true) {
		$q = $q->where("state_id", $yes ? "=" : "!=", 1);
		if ($yes) {
			$q->orWhere(function($q2) {
				$q2->where("state_id", 2)
				->whereHas("liveStream", function($q3) {
					$q3->accessible();
				}, "=", 0);
			});
		}
		
		return $q;
	}
	
	public function scopeLive($q, $yes=true) {
		if ($yes) {
			$q->whereHas("liveStream", function($q2) {
				$q2->accessible();
			});
		}
		$q = $q->where("state_id", $yes ? "=" : "!=", 2);
		return $q;
	}
	
	public function scopeShowOver($q, $yes=true) {
		return $q->where("state_id", $yes ? "=" : "!=", 3);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function hasJustBecomeLive() {
		return $this->isLive() && (!$this->exists || !$this->isLive(LiveStreamStateDefinition::find($this->original["state_id"])));
	}
}
