<?php namespace uk\co\la1tv\website\models;

use Carbon;

class WatchingNow extends MyEloquent {
	
	protected $table = 'watching_now';
	protected $fillable = array('session_id', 'playing', 'last_play_time');

	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			
			if ($model->playing) {
				$model->last_play_time = Carbon::now();
			}
			return true;
		});
	}

	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}

	public function getDates() {
		return array_merge(parent::getDates(), array('last_play_time'));
	}
	
}