<?php namespace uk\co\la1tv\website\models;

use Exception;
use Carbon;

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
		return self::where("type", "vod")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
	}

	public static function getStreamViewCount($mediaItemId) {
		return self::where("type", "live")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
	}

	public static function getNumWatchingNow($mediaItemId) {
		$cutOffTime = Carbon::now()->subSeconds(30);
		return self::where("media_item_id", $mediaItemId)->where("playing", true)->where("created_at", ">", $cutOffTime)->distinct("session_id")->count("session_id");
	}
}