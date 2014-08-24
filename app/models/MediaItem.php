<?php namespace uk\co\la1tv\website\models;

class MediaItem extends MyEloquent {
	
	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'enabled');
	protected $appends = array("related_items_for_orderable_select", "related_items_for_input");

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
	
	public function getRelatedItems() {
		$data = array();
		$items = $this->relatedItems()->orderBy("related_item_to_media_item.position", "asc")->get();
		foreach($items as $a) {
			$data[] = array(
				"id"		=> intval($a->id),
				"name"		=> $a->name
			);
		}
		return $data;
	}
	
	public function getRelatedItemsForOrderableListAttribute() {
		$data = array();
		foreach($this->getRelatedItems() as $a) {
			$data[] = array(
				"id"		=> $a['id'],
				"text"		=> $a['name']
			);
		}
		return json_encode($data);
	}
	
	public static function isValidRelatedItemsFromInput($stringFromInput) {
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
	public static function generateRelatedItemsForOrderableList($stringFromInput) {
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
	
	public function getRelatedItemsForInputAttribute() {
		$ids = array();
		foreach($this->getRelatedItems() as $a) {
			$ids[] = $a['id'];
		}
		return json_encode($ids);
	}
	
	
	public function getIsAccessible() {
		if (!$this->enabled) {
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
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function isDeletable() {
		// there is currently no condition that should prevent a media item being deleted.
		// the database relation foreign key constraints should handle deletion of related records
		return true;
	}
}