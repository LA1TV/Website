<?php namespace uk\co\la1tv\website\models;

use FormHelpers;
use Carbon;
use Config;
use DB;
use Cache;
use Auth;
use uk\co\la1tv\website\helpers\reorderableList\ChaptersReorderableList;

class MediaItemVideo extends MyEloquent {

	protected $table = 'media_items_video';
	protected $fillable = array('time_recorded', 'enabled', 'available_event_triggered');
	protected $appends = array("time_recorded_for_input", "chapters_for_reorderable_list", "chapters_for_input");
	
	
	protected static function boot() {
		parent::boot();
		
		self::saving(function($model) {
			// transaction committed in saved event
			// transaction important because entries removed in dvr_live_stream_uris
			// depending if vod is available. both the vod going live and dvr entries
			// being removed must succeed
			DB::beginTransaction();
		});
		
		self::saved(function($model) {
			if ($model->getIsLive()) {
				// remove any dvr recordings from the stream belonging to this media item
				// if there are any
				$liveStreamItem = $model->mediaItem->liveStreamItem;
				if (!is_null($liveStreamItem)) {
					$liveStreamItem->removeDvrs();
				}
			}
			
			// transaction starts in save event
			DB::commit();
		});
	}
	
	
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function chapters() {
		return $this->hasMany(self::$p.'MediaItemVideoChapter', 'media_item_video_id');
	}
	
	public function getTimeRecordedForInputAttribute() {
		if (is_null($this->time_recorded)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->time_recorded->timestamp);
	}
	
	// returns the uris to the different renders of the video
	public function getQualitiesWithUris() {
		// cache for 10 seconds
		// Urls may be different depending on the logged in (cms) user depending on the permissions the user has
		// so different caches are needed per (cms) user.
		$user = Auth::getUser();
		$cacheKeyUserId = !is_null($user) ? intval($user->id) : -1;
		return Cache::remember("mediaItemVideo.".$cacheKeyUserId.".".$this->id.".qualitiesWithUris", 10, function() {

			$sourceFile = $this->sourceFile;
			
			if (is_null($sourceFile) || !$sourceFile->getShouldBeAccessible()) {
				return array();
			}
			
			$renders = $sourceFile->renderFiles;
			$qualities = array();
			$positions = array();
			foreach($renders as $a) {
				
				$videoFile = $a->videoFile;
				if (is_null($videoFile)) {
					// this file is not a render. e.g. could be thumbnail
					continue;
				}
				
				$uris = array();

				// hls must be before dash because safari 8.0.6 has issues playing the dash version
				$videoFileHls = $videoFile->videoFileHls;
				if (!is_null($videoFileHls)) {
					// there is a hls render for this as well
					$uris[] = array(
						"uri"	=> $videoFileHls->playlistFile->getUri(),
						"type"	=> "application/x-mpegURL",
						"supportedDevices"	=> null
					);
				}

				// shouldn't need dash anymore because should be covered with hls (hlsjs),
				// and the standard mp4 for devices that can't do hls at all
				/*
				$videoFileDash = $videoFile->videoFileDash;
				if (!is_null($videoFileDash)) {
					// there is a dash render for this as well
					$uris[] = array(
						"uri"	=> $videoFileDash->mediaPresentationDescriptionFile->getUri(),
						"type"	=> "application/dash+xml",
						"supportedDevices"	=> null
					);
				}
				*/

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
			// reorder so in qualities order with dash entries first
			array_multisort($positions, SORT_NUMERIC, SORT_ASC, $qualities);
			return $qualities;
		}, true);
	}
	
	public function getScrubThumbnails() {
		// cache for 30 seconds
		// Urls may be different depending on the logged in (cms) user depending on the permissions the user has
		// so different caches are needed per (cms) user.
		$user = Auth::getUser();
		$cacheKeyUserId = !is_null($user) ? intval($user->id) : -1;
		return Cache::remember("mediaItemVideo.".$cacheKeyUserId.".".$this->id.".scrubThumbnails", 30, function() {

			$sourceFile = $this->sourceFile;
			
			if (is_null($sourceFile) || !$sourceFile->getShouldBeAccessible()) {
				return array();
			}
		
			$renders = $sourceFile->renderFiles;
			$thumbnails = array();
			$times = array();
			foreach($renders as $a) {
				$thumbnailFile = $a->videoScrubThumbnailFile;
				if (is_null($thumbnailFile)) {
					// this file is not a thumbnail.
					continue;
				}
				$time = intval($thumbnailFile->time);
				$times[] = $time;
				$thumbnails[] = array(
					"uri"	=> $a->getUri(),
					"time"	=> $time
				);
			}
			array_multisort($times, SORT_NUMERIC, SORT_ASC, $thumbnails);
			return $thumbnails;
		}, true);
	}

	public function getViewCount() {
		return PlaybackHistory::getVodViewCount(intval($this->media_item_id));
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('time_recorded'));
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

	public function getDuration() {
		$file = $this->sourceFile;
		if (is_null($file)) {
			return null;
		}
		$vodData = $file->vodData;
		return $vodData->duration;
	}

	public function getDurationPretty() {
		$duration = $this->getDuration();
		if (is_null($duration)) {
			return null;
		}
		$duration = round($duration);
		$s = $duration % 60;
		$m = ($duration / 60) % 60;
		$h = floor($duration / 3600);
		$result = "";
		if ($h > 0) {
			$result .= $h.":";
		}
		$result .= str_pad($m, 2, "0", STR_PAD_LEFT).":";
		$result .= str_pad($s, 2, "0", STR_PAD_LEFT);
		return $result;
	}
	
	private function getChaptersDataForReorderableList() {
		$chapterModels = $this->chapters()->orderBy("time", "asc")->orderBy("title", "asc")->get();
		$data = array();
		foreach($chapterModels as $a) {
			$data[] = array(
				"title"	=> $a->title,
				"time"	=> intval($a->time)
			);
		}
		return $data;
	}	

	public function getChaptersForReorderableListAttribute() {
		return self::generateInitialDataForChaptersReorderableList($this->getChaptersDataForReorderableList());
	}
	
	public function getChaptersForInputAttribute() {
		return self::generateInputValueForChaptersReorderableList($this->getChaptersDataForReorderableList());
	}
	
	public static function isValidDataFromChaptersReorderableList($data) {
		$reorderableList = new ChaptersReorderableList($data);
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForChaptersReorderableList($data) {
		$reorderableList = new ChaptersReorderableList($data);
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForChaptersReorderableList($data) {
		$reorderableList = new ChaptersReorderableList($data);
		return $reorderableList->getStringForInput();
	}
	
}