<?php namespace uk\co\la1tv\website\models;

class MediaItem extends MyEloquent {
	
	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'enabled');
	
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
	
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		
		// check that it's in a playlist that is accessible
		foreach($this->playlists() as $a) {
			if ($a->getIsAccessible()) {
				return true;
			}
		}
		return false;
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}