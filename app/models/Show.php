<?php namespace uk\co\la1tv\website\models;

class Show extends MyEloquent {

	protected $table = 'shows';
	protected $fillable = array('name', 'enabled', 'description');
	
	public function playlists() {
		return $this->hasMany(self::$p.'Playlist', 'show_id');
	}
	
	// returns true if this show should be accessible now. I.e enabled
	public function getIsAccessible() {
		return (boolean) $this->enabled;
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	// scopes to contain shows that are considered as active.
	public function scopeActive($q) {
		// TODO
		return $q;
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true);
	}
	
	public function isDeletable() {
		return $this->playlists()->count() === 0;
	}
}