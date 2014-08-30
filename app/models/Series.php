<?php namespace uk\co\la1tv\website\models;

class Series extends MyEloquent {

	protected $table = 'series';
	protected $fillable = array('name', 'enabled', 'description');
	
	public function playlists() {
		return $this->hasMany(self::$p.'Playlist', 'series_id');
	}
	
	// returns true if this series should be accessible now. I.e enabled and has a playlist that is accessible
	public function getIsAccessible() {
		
		if (!$this->enabled) {
			return false;
		}
		
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
		return $this->playlists()->count() === 0;
	}
}