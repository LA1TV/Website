<?php namespace uk\co\la1tv\website\models;

use FormHelpers;
use \Session;
use Carbon;
use Config;

class MediaItemVideo extends MyEloquent {

	protected $table = 'media_items_video';
	protected $fillable = array('time_recorded', 'enabled');
	protected $appends = array("time_recorded_for_input");
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function getTimeRecordedForInputAttribute() {
		if (is_null($this->time_recorded)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->time_recorded->timestamp);
	}
	
	// returns the uris to the different renders of the video
	public function getQualitiesWithUris() {
		
		$sourceFile = $this->sourceFile;
		
		if (is_null($sourceFile) || !$sourceFile->getShouldBeAccessible()) {
			return array();
		}
	
		$renders = $sourceFile->renderFiles;
		$qualities = array();
		$positions = array();
		foreach($renders as $a) {
			
			$uris = array();
			$uris[] = array(
				"uri"	=> $a->getUri(),
				"type"	=> "video/mp4",
				"supportedDevices"	=> null
			);
			
			$positions[] = intval($a->videoFile->qualityDefinition->position);
			$qualities[] = array(
				"qualityDefinition"		=> $a->videoFile->qualityDefinition,
				"uris"					=> $uris
			);
		}
		// reorder so in qualities order
		array_multisort($positions, SORT_NUMERIC, SORT_ASC, $qualities);
		return $qualities;
	}
	
	public function registerViewCount() {
	
		if (!$this->getIsAccessible() || !$this->getIsLive()) {
			// shouldn't be accessible or isn't live
			return;
		}
		
		$sessionKey = "viewCount-".$this->id;
		$lastTimeRegistered = Session::get($sessionKey, null);
		if (!is_null($lastTimeRegistered) && $lastTimeRegistered >= Carbon::now()->subMinutes(Config::get("custom.interval_between_registering_view_counts"))->timestamp) {
			// already registered view not that long ago.
			return;
		}
		$this->increment("view_count");
		Session::set($sessionKey, Carbon::now()->timestamp);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('time_recorded', 'scheduled_publish_time'));
	}
	
	// returns true if this should be shown with the parent media item. If false it should look like the MediaItem does not have a video component.
	public function getIsAccessible() {
		return $this->enabled && $this->mediaItem->getIsAccessible();
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("mediaItem", function($q2) {
			$q2->accessible();
		});
	}
	
	// returns true if the video should be live to watch.
	// this is the case when the parent media items scheduled publish time has passed
	// the video component can be accessible but not live.
	public function getIsLive() {
		if (!$this->getIsAccessible()) {
			return false;
		}
		if (!is_null($this->sourceFile) && !$this->sourceFile->getFinishedProcessing()) {
			return false;
		}
		if (is_null($this->mediaItem->scheduled_publish_time)) {
			return true;
		}
		return $this->mediaItem->scheduled_publish_time->isPast();
	}
	
	public function scopeLive($q) {
		return $q->accessible()->whereHas("mediaItem", function($q2) {
			$q2->where(function($q3) {
				$q3->whereNull("scheduled_publish_time")
				->orWhere("scheduled_publish_time", "<", Carbon::now());
			});
		})->where(function($q2) {
			$q2->has("sourceFile", "=", 0)
			->orWhereHas("sourceFile", function($q3) {
				$q3->finishedProcessing();
			});
		});
	}
	
}