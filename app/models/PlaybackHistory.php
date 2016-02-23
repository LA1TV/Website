<?php namespace uk\co\la1tv\website\models;

use Exception;
use Carbon;
use DB;
use SmartCache;

class PlaybackHistory extends MyEloquent {
	
	protected $table = 'playback_history';
	protected $fillable = array('session_id', 'original_session_id', 'type', 'playing', 'time', 'constitutes_view');

	protected static function boot() {
		parent::boot();
		
		self::creating(function($model) {
			$model->original_session_id = $model->session_id;
			return true;
		});

		self::saving(function($model) {
			if ($model->type !== "vod" && $model->type !== "live") {
				throw(new Exception("Invalid type."));
			}
			return true;
		});
	}

	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function vodSourceFile() {
		return $this->belongsTo(self::$p.'File', 'vod_source_file_id');
	}

	public function user() {
		return $this->belongsTo(self::$p.'User', 'user_id');
	}

	public static function getVodViewCount($mediaItemId) {
		$key = "vodViewCount.".$mediaItemId;
		// cache for 20 seconds (renew in background when 10 seconds old)
		$seconds = 20;
		$closure = function() use (&$mediaItemId) {
			return PlaybackHistory::where("type", "vod")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
		};
		return SmartCache::get($key, $seconds, $closure);
	}

	public static function getStreamViewCount($mediaItemId) {
		$key = "streamViewCount.".$mediaItemId;
		// cache for 20 seconds (renew in background when 10 seconds old)
		$seconds = 20;
		$closure = function() use (&$mediaItemId) {
			return PlaybackHistory::where("type", "live")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
		};
		return SmartCache::get($key, $seconds, $closure);
	}

	public static function getNumWatchingNow($mediaItemId) {
		$key = "numWatchingNow.".$mediaItemId;
		// cache for 12 seconds (renew in background when 6 seconds old)
		$seconds = 12;
		$closure = function() use (&$mediaItemId) {
			$cutOffTime = Carbon::now()->subSeconds(30);
			return PlaybackHistory::where("media_item_id", $mediaItemId)->where("playing", true)->where("created_at", ">", $cutOffTime)->distinct("session_id")->count("session_id");
		};
		return SmartCache::get($key, $seconds, $closure);
	}

	public static function getNumWatchingNowByMediaItem() {
		$key = "numWatchingByMediaItem";
		// cache for 12 seconds (renew in background when 6 seconds old)
		$seconds = 12;
		$closure = function() {
			$cutOffTime = Carbon::now()->subSeconds(30);
			$records = PlaybackHistory::select(DB::raw('media_item_id, COUNT(DISTINCT session_id) as count'))
				->where("playing", true)
				->where("created_at", ">", $cutOffTime)
				->groupBy("media_item_id")
				->get();
			$results = array();
			foreach ($records as $a) {
				$results[] = array(
					"id"	=> intval($a->media_item_id),
					"count"	=> intval($a->count)
				);
			}
			return $results;
		};
		return SmartCache::get($key, $seconds, $closure);
	}
}