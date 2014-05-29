<?php namespace uk\co\la1tv\website\models;

use Exception;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'series_no');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->series_id === NULL) {
				if ($model->name === NULL) {
					throw(new Exception("A name must be specified"));
				}
				else if ($model->series_no !== NULL) {
					throw(new Exception("A standard playlist cannot have a series number."));
				}
			}
			else {
				if ($model->series_no === NULL) {
					throw(new Exception("Series number required."));
				}
			}
			return true;
		});
	}
	
	public function series() {
		return $this->belongsTo(self::$p.'Series', 'series_id');
	}
	
	public function sideBannerFile() {
		return $this->belongsTo(self::$p.'file', 'side_banner_file_id');
	}
	
	public function coverFile() {
		return $this->belongsTo(self::$p.'file', 'cover_file_id');
	}
	
	public function mediaItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'media_item_to_playlist', 'playlist_id', 'media_item_id')->withPivot('position', 'from_playlist_id');
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('scheduled_publish_time'));
	}
	
	// returns true if this playlist should be accessible now. I.e enabled and scheduled_publish_time passed and series enabled if part of series etc
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		if (!is_null($this->series()->first())) {
			if (!$this->series()->first()->enabled) {
				return false;
			}
		}
		return $this->scheduled_publish_time->getTimestamp() >= time();
	}
}