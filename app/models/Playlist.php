<?php namespace uk\co\la1tv\website\models;

use Exception;
use FormHelpers;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'series_no');
	protected $appends = array("scheduled_publish_time_for_input", "serialized_playlist_content", "playlist_content_for_input");
	
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
	
	public function coverArtFile() {
		return $this->belongsTo(self::$p.'File', 'cover_art_file_id');
	}
	
	public function mediaItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'media_item_to_playlist', 'playlist_id', 'media_item_id')->withPivot('position', 'from_playlist_id');
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	public function getPlaylistContent() {
		$data = array();
		$items = $this->mediaItems()->orderBy("media_item_to_playlist.position", "asc")->get();
		foreach($items as $a) {
			$data[] = array(
				"id"		=> intval($a->id),
				"name"		=> $a->name
			);
		}
		return $data;
	}
	
	public function getSerializedPlaylistContentAttribute() {
		return json_encode($this->getPlaylistContent());
	}
	
	public function getPlaylistContentForInputAttribute() {
		$ids = array();
		foreach($this->getPlaylistContent() as $a) {
			$ids[] = $a['id'];
		}
		return implode(",", $ids);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('scheduled_publish_time'));
	}
	
	// returns true if this playlist should be accessible now. I.e enabled and scheduled_publish_time passed and series enabled if part of series etc
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		if (!is_null($this->series)) {
			if (!$this->series()->first()->enabled) {
				return false;
			}
		}
		return $this->scheduled_publish_time->getTimestamp() >= time();
	}
}