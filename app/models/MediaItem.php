<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;
use FormHelpers;
use Carbon;
use Exception;

class MediaItem extends MyEloquent {
	
	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'enabled', 'scheduled_publish_time');
	protected $appends = array("related_items_for_orderable_select", "related_items_for_input", "scheduled_publish_time_for_input");
	
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
	
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		
		$scheduledPublishTime = $this->scheduled_publish_time;
		if (!is_null($scheduledPublishTime) && !$scheduledPublishTime->isPast()) {
			return false;
		}
		
		// check that it's in a playlist that is accessible
		foreach($this->playlists as $a) {
			if ($a->getIsAccessible()) {
				return true;
			}
		}
		return false;
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->whereHas("playlists", function($q2) {
			$q2->accessible();
		})
		->where(function($q2) {
			$q2->whereNull("scheduled_publish_time")
			->orWhere("scheduled_publish_time", "<", Carbon::now());
		});
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