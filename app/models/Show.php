<?php namespace uk\co\la1tv\website\models;

use Config;
use Cache;
use URL;
use DB;

class Show extends MyEloquent {

	protected $table = 'shows';
	protected $fillable = array('name', 'enabled', 'description', 'pending_search_index_version', 'current_search_index_version', 'in_index');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			// transaction ended in "saved" event
			// needed to make sure if search index version number is incremented it
			// takes effect at the same time that the rest of the media item is updated
			DB::beginTransaction();

			// assume that something has changed and force ths item to be reindexed
			$a = Show::with("playlists", "playlists.mediaItems")->find(intval($model->id));
			// $a may be null if this item is currently being created
			// when the item is being created pending_search_index_version defaults to 1
			// meaning the item will be indexed
			if (!is_null($a)) {
				// make sure get latest version number. The version in $model might have changed before the transaction started
				$currentPendingIndexVersion = intval($a->pending_search_index_version);
				$model->pending_search_index_version = $currentPendingIndexVersion+1;

				// also force all playlists liked to this and media items in them to be reindexed
				// because media items and playlists contain show information in their indexes
				foreach($a->playlists as $playlist) {
					$playlist->pending_search_index_version += 1;
					$playlist->save();
					foreach($playlist->mediaItems as $mediaItem) {
						$mediaItem->pending_search_index_version += 1;
						$mediaItem->save();
					}
				}
			}
			return true;
		});

		self::saved(function($model) {
			DB::commit();
		});
	}

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
	
	// returns the side banner from the highest series that has one, or null otherwise
	public function getSideBannerUri($width, $height) {
		foreach($this->playlists()->accessibleToPublic()->orderBy("series_no", "desc")->get() as $a) {
			$sideBannerUri = $a->getSideBannerUri($width, $height);
			if (!is_null($sideBannerUri)) {
				return $sideBannerUri;
			}
		}
		return null;
	}
	
	// returns the side banner fill from the highest series that has one, or null otherwise
	public function getSideBannerFillUri($width, $height) {
		foreach($this->playlists()->accessibleToPublic()->orderBy("series_no", "desc")->get() as $a) {
			$sideBannerFillUri = $a->getSideBannerFillUri($width, $height);
			if (!is_null($sideBannerFillUri)) {
				return $sideBannerFillUri;
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

	public function scopeNeedsReindexing($q) {
		return $q->whereRaw("`shows`.`pending_search_index_version` != `shows`.`current_search_index_version`");
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