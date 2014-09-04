<?php namespace uk\co\la1tv\website\models;

use Exception;
use FormHelpers;
use Carbon;
use Config;
use Cache;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'series_no');
	protected $appends = array("scheduled_publish_time_for_input", "playlist_content_for_orderable_list", "playlist_content_for_input");
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->enabled && is_null($model->scheduled_publish_time)) {
				throw(new Exception("A Playlist which is enabled must have a scheduled publish time."));
			}
			else if ($model->show_id === NULL) {
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
	
	public function show() {
		return $this->belongsTo(self::$p.'Show', 'show_id');
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
	
	// returns the media items name with an episode number if this playlist is a series in a show
	public function generateEpisodeTitle($mediaItemParam) {
		$mediaItem = $this->mediaItems->find($mediaItemParam->id);
		if (is_null($mediaItem)) {
			throw(new Exception("Playlist does not contain MediaItem."));
		}
		
		if (is_null($this->show)) {
			return $mediaItem->name;
		}
		
		return ($mediaItem->pivot->position + 1) . ". " . $mediaItem->name;
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
	
	public static function getCachedActivePlaylists($includingShows) {
		if ($includingShows) {
			return Cache::remember('activePlaylists', Config::get("custom.cache_time"), function() {
				return self::active()->orderBy("name", "asc")->get();
			});
		}
		else {
			return Cache::remember('activePlaylistsExcludingShows', Config::get("custom.cache_time"), function() {
				return self::belongsToShow(false)->active()->orderBy("name", "asc")->get();
			});
		}
	}
	
	// A playlist is active when:
	//						it's scheduled publish time is not too old (configured in config), or one of the following is true
	//						it contains an active media item.
	public function scopeActive($q) {
		$startTime = Carbon::now()->subDays(Config::get("custom.num_days_active"));
		return $q->accessible()->where(function($q2) use (&$startTime) {
			$q2->where("scheduled_publish_time", ">=", $startTime)
			->orWhereHas("mediaItems", function($q3) {
				$q3->accessible()->active();
			});
		});
	}
	
	public function scopeBelongsToShow($q, $yes=true) {
		return $q->has("show", $yes ? "!=" : "=", 0);
	}
	
	// get the uri that should be used as the media items cover art.
	// if the media item has one it returns that, otherwise it returns the playlist one if it has one
	// returns null if there isn't one.
	public function getMediaItemCoverArtUri($mediaItemParam) {
		$mediaItem = $this->mediaItems()->find($mediaItemParam->id);
		if (is_null($mediaItem)) {
			throw(new Exception("The media item is not part of the playlist."));
		}
		$coverArtFile = $mediaItem->coverArtFile;
		dd($coverArtFile);
	}
	
	// returns true if this playlist should be accessible now. I.e enabled and scheduled_publish_time passed and show enabled if part of show etc
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		if (!is_null($this->show)) {
			if (!$this->show->enabled) {
				return false;
			}
		}
		$scheduledPublishTime = $this->scheduled_publish_time;
		return is_null($scheduledPublishTime) || $scheduledPublishTime->isPast();
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->where(function($q2) {
			$q2->has("show", "=", 0)
			->orWhereHas("show", function($q3) {
				$q3->accessible();
			});
		})
		->where("scheduled_publish_time", "<", Carbon::now());
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}