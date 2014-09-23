<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;
use FormHelpers;
use Carbon;
use Exception;
use Config;
use DB;

class MediaItem extends MyEloquent {
	
	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'enabled', 'scheduled_publish_time');
	protected $appends = array("related_items_for_orderable_list", "related_items_for_input", "scheduled_publish_time_for_input");
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			
			if ($model->enabled && is_null($model->scheduled_publish_time)) {
				throw(new Exception("A MediaItem which is enabled must have a scheduled publish time."));
			}
			return true;
		});
	}
	
	public function comments() {
		return $this->hasMany(self::$p.'MediaItemComment', 'media_item_id');
	}

	public function likes() {
		return $this->hasMany(self::$p.'MediaItemLike', 'media_item_id');
	}
	
	public function liveStreamItem() {
		return $this->hasOne(self::$p.'MediaItemLiveStream', 'media_item_id');
	}
	
	public function videoItem() {
		return $this->hasOne(self::$p.'MediaItemVideo', 'media_item_id');
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
	
	public function playlists() {
		return $this->belongsToMany(self::$p.'Playlist', 'media_item_to_playlist', 'media_item_id', 'playlist_id')->withPivot('position', 'from_playlist_id');
	}
	
	public function relatedItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_media_item', 'media_item_id', 'related_media_item_id')->withPivot('position');
	}
	
	public function itemsRelatedTo() {
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_media_item', 'related_media_item_id', 'media_item_id')->withPivot('position');
	}
	
	private function getRelatedItemIdsForReorderableList() {
		$ids = array();
		$items = $this->relatedItems()->orderBy("related_item_to_media_item.position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getRelatedItemsForOrderableListAttribute() {
		return self::generateInitialDataForAjaxSelectOrderableList($this->getRelatedItemIdsForReorderableList());
	}
	
	public function getRelatedItemsForInputAttribute() {
		return self::generateInputValueForAjaxSelectOrderableList($this->getRelatedItemIdsForReorderableList());
	}
	
	public static function isValidIdsFromAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->getStringForInput();
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	public function registerLike($siteUser) {
		return $this->registerLikeDislike($siteUser, true);
	}
	
	public function registerDislike($siteUser) {
		return $this->registerLikeDislike($siteUser, false);
	}
	
	private function registerLikeDislike($siteUser, $isLike) {
		return DB::transaction(function() use (&$isLike, &$siteUser) {
			$like = $this->likes()->where("site_user_id", $siteUser->id)->first();
			if (is_null($like)) {
				$like = new MediaItemLike(array(
					"is_like"	=> $isLike
				));
				$like->siteUser()->associate($siteUser);
				$this->likes()->save($like);
				return true;
			}
			else if ((boolean) $like->is_like !== $isLike) {
				$like->is_like = $isLike;
				$like->save();
				return true;
			}
			return false;
		});
	}
	
	public function removeLike($siteUser) {
		return $this->likes()->where("site_user_id", $siteUser->id)->delete() > 0;
	}
	
	// Get the first one that has a show if there is one, or just the first one otherwise
	public function getDefaultPlaylist() {
		$this->load("playlists", "playlists.show");
		$playlist = null;
		foreach($this->playlists as $a) {
			if (is_null($playlist)) {
				$playlist = $a;
			}
			if (!is_null($a->show)) {
				$playlist = $a;
				break;
			}
		}
		return $playlist;
	}
	
	// returns true if this media item should be accessible
	// this does not take into consideration the publish time. A media item should still be accessible even if the publish time hasn't passed.
	// If the publish time hasn't passed then and there's a MediaItemVideo attached it should not be watchable until after this time.
	// same applies to a live stream (although with a live stream there is no actual restriction, the stream could start earlier/later)
	public function getIsAccessible() {
		
		if (!$this->enabled) {
			return false;
		}
		if ($this->playlists()->accessible()->count() === 0) {
			return false;
		}
		$sideBannerFile = $this->sideBannerFile;
		if (!is_null($sideBannerFile) && !$sideBannerFile->getFinishedProcessing()) {
			return false;
		}
		$coverFile = $this->coverFile;
		if (!is_null($coverFile) && !$coverFile->getFinishedProcessing()) {
			return false;
		}
		$coverArtFile = $this->coverArtFile;
		if (!is_null($coverArtFile) && !$coverArtFile->getFinishedProcessing()) {
			return false;
		}
		return true;
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("playlists", function($q2) {
			$q2->accessible();
		})->where(function($q2) {
			$q2->has("sideBannerFile", "=", 0)
			->orWhereHas("sideBannerFile", function($q3) {
				$q3->finishedProcessing();
			});
		})->where(function($q2) {
			$q2->has("coverFile", "=", 0)
			->orWhereHas("coverFile", function($q3) {
				$q3->finishedProcessing();
			});
		})->where(function($q2) {
			$q2->has("coverArtFile", "=", 0)
			->orWhereHas("coverArtFile", function($q3) {
				$q3->finishedProcessing();
			});
		});
	}
	
	// A media item is active when:
	//						it's scheduled publish time is not too old (configured in config)
	//						the scheduled publish time is before some time in the future (configured in config)
	//						the scheduled publish time is automatically set if not specified the first time a media item is enabled.
	public function scopeActive($q) {
		$startTime = Carbon::now()->subDays(Config::get("custom.num_days_active"));
		$endTime = Carbon::now()->addDays(Config::get("custom.num_days_future_before_active"));
		return $q->accessible()->where("scheduled_publish_time", ">=", $startTime)->where("scheduled_publish_time", "<", $endTime);
	}
	
	public function scopeScheduledPublishTimeBetweenDates($q, $start, $end) {
		return $q->whereNotNull("scheduled_publish_time")->where("scheduled_publish_time", ">=", $start)->where("scheduled_publish_time", "<", $end);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function getDates() {
		return array_merge(parent::getDates(), array('scheduled_publish_time'));
	}
	
	public function isDeletable() {
		// there is currently no condition that should prevent a media item being deleted.
		// the database relation foreign key constraints should handle deletion of related records
		return true;
	}
}