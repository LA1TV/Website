<?php namespace uk\co\la1tv\website\models;

use Config;
use Cache;
use URL;

class Show extends MyEloquent {

	protected $table = 'shows';
	protected $fillable = array('name', 'enabled', 'description');
	
	public function playlists() {
		return $this->hasMany(self::$p.'Playlist', 'show_id');
	}
	
	public function getUri() {
		return URL::route('show', array($this->id));
	}
	
	// returns the cover from the highest series that has one, or null otherwise
	public function getCoverUri($width, $height) {
		foreach($this->playlists()->accessibleToPublic()->orderBy("series_no", "desc")->get() as $a) {
			$coverUri = $a->getCoverUri($width, $height);
			if (!is_null($coverUri)) {
				return $coverUri;
			}
		}
		return null;
	}
	
	// returns the cover art from the highest series that has one, or the default cover otherwise
	public function getCoverArtUri($width, $height) {
		foreach($this->playlists()->accessibleToPublic()->orderBy("series_no", "desc")->get() as $a) {
			$coverArtUri = $a->getCoverArtUri($width, $height);
			if (!is_null($coverArtUri)) {
				return $coverArtUri;
			}
		}
		// return default cover
		return Config::get("custom.default_cover_uri");
	}
	
	// returns true if this show should be accessible now. I.e enabled
	public function getIsAccessible() {
		return (boolean) $this->enabled;
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	// scopes to contain shows that are considered as active.
	// A show is active when: 
	//						it is linked to a playlist that is active
	public function scopeActive($q) {
		return $q->accessible()->whereHas("playlists", function($q2) {
			$q2->accessibleToPublic()->active();
		});
	}
	
	public static function getCachedActiveShows() {
		return Cache::remember('activeShows', Config::get("custom.cache_time"), function() {
			return self::active()->orderBy("name", "asc")->get();
		});
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true);
	}
	
	public function isDeletable() {
		return $this->playlists()->count() === 0;
	}
}