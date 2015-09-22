<?php namespace uk\co\la1tv\website\models;

use Exception;

class PlaybackHistory extends MyEloquent {
	
	protected $table = 'playback_history';
	protected $fillable = array('session_id', 'original_session_id', 'type', 'playing', 'last_play_time' ,'time', 'constitutes_as_view';

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

	public function getDates() {
		return array_merge(parent::getDates(), array('last_play_time'));
	}
	
	public static function getVodViewCount($mediaItemId) {
		return self::where("type", "vod")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
	}

	public static function getStreamViewCount($mediaItemId) {
		return self::where("type", "stream")->where("media_item_id", $mediaItemId)->where("constitutes_view", true)->count();
	}
}