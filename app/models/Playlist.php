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
	
	public function getPlaylistContentForOrderableListAttribute() {
		$data = array();
		foreach($this->getPlaylistContent() as $a) {
			$data[] = array(
				"id"		=> $a['id'],
				"text"		=> $a['name']
			);
		}
		return json_encode($data);
	}
	
	public static function isValidPlaylistDataFromInput($stringFromInput) {
		$data = json_decode($stringFromInput, true);
		if (!is_array($data)) {
			return false;
		}
		$ids = array();
		foreach($data as $a) {
			if (!is_int($a) && !is_null($a)) {
				return false;
			}
			if (in_array($a, $ids, true)) {
				return false;
			}
			else {
				$ids[] = $a;
			}
		}
		if (count($ids) === 0) {
			return true;
		}
		return MediaItem::whereIn("id", $ids)->count() === count($ids);
	}
	
	// should be the string from the input
	public static function generatePlaylistContentForOrderableList($stringFromInput) {
		$data = json_decode($stringFromInput, true);
		if (!is_array($data)) {
			return "[]";
		}
		$output = array();
		$ids = array();
		foreach($data as $a) {
			if (is_int($a) && !in_array($a, $ids, true)) {
				$ids[] = $a;
			}
			$output[] = array(
				"id"	=> is_int($a) ? $a : null,
				"text"	=> null
			);
		}
		if (count($ids) > 0) {
			$mediaItems = MediaItem::whereIn("id", $ids)->get();
			$mediaItemIds = array();
			foreach($mediaItems as $a) {
				$mediaItemIds[] = intval($a->id);
			}
			foreach($output as $i=>$a) {
				if (is_null($a['id'])) {
					continue;
				}
				$mediaItemIndex = array_search($a['id'], $mediaItemIds, true);
				if ($mediaItemIndex === false) {
					$output[$i]["id"] = null; // if the media item can't be found anymore make the id null as well.
					continue;
				}
				$output[$i]["text"] = $mediaItems[$mediaItemIndex]->name;
			}
		}
		return json_encode($output);
	}
	
	public function getPlaylistContentForInputAttribute() {
		$ids = array();
		foreach($this->getPlaylistContent() as $a) {
			$ids[] = $a['id'];
		}
		return json_encode($ids);
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