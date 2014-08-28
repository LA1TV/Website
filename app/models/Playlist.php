<?php namespace uk\co\la1tv\website\models;

use Exception;
use FormHelpers;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'series_no');
	protected $appends = array("scheduled_publish_time_for_input", "playlist_content_for_orderable_select", "playlist_content_for_input");
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->series_id === NULL) {
				if ($model->name === NULL) {
					throw(new Exception("A name must be specified."));
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
		return $this->belongsTo(self::$p.'File', 'side_banner_file_id');
	}
	
	public function coverFile() {
		return $this->belongsTo(self::$p.'File', 'cover_file_id');
	}
	
	public function coverArtFile() {
		return $this->belongsTo(self::$p.'File', 'cover_art_file_id');
	}
	
	public function mediaItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'media_item_to_playlist', 'playlist_id', 'media_item_id')->withPivot('position', 'from_playlist_id');
	}
	
	public function relatedItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_playlist', 'media_item_id', 'related_media_item_id')->withPivot('position');
	}
	
	public function itemsRelatedTo() {
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_playlist', 'related_media_item_id', 'media_item_id')->withPivot('position');
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	private function getPlaylistContentIdsForOrderableList() {
		$ids = array();
		$items = $this->mediaItems()->orderBy("media_item_to_playlist.position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getPlaylistContentForInputAttribute() {
		return MediaItem::generateInputValueForAjaxSelectOrderableList($this->getPlaylistContentIdsForOrderableList());
	}
	
	public function getPlaylistContentForOrderableListAttribute() {
		return MediaItem::generateInitialDataForAjaxSelectOrderableList($this->getPlaylistContentIdsForOrderableList());
	}
	
	private function getRelatedItemsIdsForOrderableList() {
		$ids = array();
		$items = $this->relatedItems()->orderBy("related_item_to_playlist.position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getRelatedItemsForInputAttribute() {
		return MediaItem::generateInputValueForAjaxSelectOrderableList($this->getRelatedItemsIdsForOrderableList());
	}
	
	public function getRelatedItemsForOrderableListAttribute() {
		return MediaItem::generateInitialDataForAjaxSelectOrderableList($this->getRelatedItemsIdsForOrderableList());
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
			if (!$this->series->enabled) {
				return false;
			}
		}
		$scheduledPublishTime = $this->scheduled_publish_time;
		return is_null($scheduledPublishTime) || $scheduledPublishTime->isPast();
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}