<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\PlaylistCreditsReorderableList;
use Exception;
use FormHelpers;
use Carbon;
use Config;
use Cache;
use URL;
use Facebook;
use DB;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'series_no', 'pending_search_index_version', 'current_search_index_version', 'in_index');
	protected $appends = array("scheduled_publish_time_for_input", "playlist_content_for_reorderable_list", "playlist_content_for_input", "related_items_for_reorderable_list", "related_items_for_input", "custom_uri_name", "credits_for_reorderable_list", "credits_for_input");
	
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

			// transaction ended in "saved" event
			// needed to make sure if search index version number is incremented it
			// takes effect at the same time that the rest of the media item is updated
			DB::beginTransaction();

			// assume that something has changed and force ths item to be reindexed
			$a = Playlist::with("mediaItems")->find(intval($model->id));
			// $a may be null if this item is currently being created
			// when the item is being created pending_search_index_version defaults to 1
			// meaning the item will be indexed
			if (!is_null($a)) {
				// make sure get latest version number. The version in $model might have changed before the transaction started
				$currentPendingIndexVersion = intval($a->pending_search_index_version);
				$model->pending_search_index_version = $currentPendingIndexVersion+1;
				// also force reindex of all items that are currently in the playlist
				foreach($a->mediaItems as $mediaItem) {
					$mediaItem->touch();
				}
			}
			return true;
		});

		self::saved(function($model) {
			DB::commit();
		});
	}
	
	public function show() {
		return $this->belongsTo(self::$p.'Show', 'show_id');
	}
	
	public function sideBannerFile() {
		return $this->belongsTo(self::$p.'File', 'side_banner_file_id');
	}
	
	public function sideBannerFillFile() {
		return $this->belongsTo(self::$p.'File', 'side_banner_fill_file_id');
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
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_playlist', 'playlist_id', 'related_media_item_id')->withPivot('position');
	}
	
	public function itemsRelatedTo() {
		return $this->belongsToMany(self::$p.'MediaItem', 'related_item_to_playlist', 'related_media_item_id', 'media_item_id')->withPivot('position');
	}
	
	public function customUri() {
		return $this->morphOne('uk\co\la1tv\website\models\CustomUri', 'uriable');
	}
	
	public function credits() {
		return $this->morphMany('uk\co\la1tv\website\models\Credit', 'creditable');
	}
	
	public function getCustomUriNameAttribute() {
		$customUri = $this->customUri;
		if (is_null($customUri)) {
			return null;
		}
		return $customUri->name;
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	// returns the name from the playlist if set, otherwise the name of the show if it's linked to a show.
	// if the show has a name use that otherwise it should be the name of the show with series number
	// shouldn't be a case where it's not set on playlist and not part of show
	public function generateName() {
		if (!is_null($this->name)) {
			return $this->name;
		}
		return $this->show->name." Series ".$this->series_no;
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
	public function generateEpisodeTitle($mediaItemParam, $includePlaylistName=false) {
		$mediaItem = $this->mediaItems->find($mediaItemParam->id);
		if (is_null($mediaItem)) {
			throw(new Exception("Playlist does not contain MediaItem."));
		}
		
		$name = $includePlaylistName ? $this->generateName() . " - " : "";
		if (is_null($this->show)) {
			$name .= $mediaItem->name;
		}
		else {
			$name .= $this->getEpisodeNumber($mediaItem) . ". " . $mediaItem->name;
		}
		return $name;
	}
	
	public function getEpisodeNumber($mediaItem) {
		foreach($this->mediaItems()->orderBy("media_item_to_playlist.position")->get() as $i=>$item) {
			if (intval($item->id) === intval($mediaItem->id)) {
				return $i+1;
			}
		}
		throw(new Exception("Playlist does not contain MediaItem."));
	}
	
	public function generateRelatedItems($mediaItem) {
		$mediaItemRelatedItems = $mediaItem->relatedItems()->accessible()->orderBy("related_item_to_media_item.position")->get();
		$playlistRelatedItems = $this->relatedItems()->accessible()->orderBy("related_item_to_playlist.position")->get();
		$items = $mediaItemRelatedItems->merge($playlistRelatedItems);
		return $items;
	}
	
	public function getPlaylistContentForInputAttribute() {
		return MediaItem::generateInputValueForAjaxSelectReorderableList($this->getPlaylistContentIdsForOrderableList());
	}
	
	public function getPlaylistContentForReorderableListAttribute() {
		return MediaItem::generateInitialDataForAjaxSelectReorderableList($this->getPlaylistContentIdsForOrderableList());
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
		return MediaItem::generateInputValueForAjaxSelectReorderableList($this->getRelatedItemsIdsForOrderableList());
	}
	
	public function getRelatedItemsForReorderableListAttribute() {
		return MediaItem::generateInitialDataForAjaxSelectReorderableList($this->getRelatedItemsIdsForOrderableList());
	}

	private function getCreditsDataForReorderableList() {
		$positions = array();
		$names = array();
		$data = array();
		$this->load("credits", "credits.productionRole", "credits.siteUser", "credits.productionRole.productionRolePlaylist");
		$items = $this->credits()->get();
		foreach($items as $a) {
			$nameOverride = $a->name_override;
			$siteUser = $a->siteUser;
			$positions[] = intval($a->productionRole->position);
			$names[] = !is_null($nameOverride) ? $nameOverride : $siteUser->name;
			$data[] = array(
				"productionRoleId"	=> intval($a->productionRole->id),
				"siteUserId"		=> !is_null($siteUser) ? intval($siteUser->id) : null,
				"nameOverride"		=> $nameOverride
			);
		}
		// sort so that credits are in the correct order
		// first by role position, then by name (because could be more than one person per role)
		array_multisort($positions, SORT_ASC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $data);
		return $data;
	}

	public function getCreditsForReorderableListAttribute() {
		return self::generateInitialDataForPlaylistCreditsReorderableList($this->getCreditsDataForReorderableList());
	}
	
	public function getCreditsForInputAttribute() {
		return self::generateInputValueForPlaylistCreditsReorderableList($this->getCreditsDataForReorderableList());
	}

	public static function isValidDataFromPlaylistCreditsReorderableList($data) {
		$reorderableList = new PlaylistCreditsReorderableList($data);
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForPlaylistCreditsReorderableList($data) {
		$reorderableList = new PlaylistCreditsReorderableList($data);
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForPlaylistCreditsReorderableList($data) {
		$reorderableList = new PlaylistCreditsReorderableList($data);
		return $reorderableList->getStringForInput();
	}
	
	public function getUri() {
		return URL::route('playlist', array($this->id));
	}
	
	// returns the uri to the page containing the playlist with media item on the main site.
	public function getMediaItemUri($mediaItem) {
		return URL::route('player', array($this->id, $mediaItem->id));
	}
	
	public function getMediaItemEmbedUri($mediaItem) {
		return URL::route('embed-player', array($this->id, $mediaItem->id));
	}
	
	public function getEmbedData($mediaItem) {
		return array(
			"embedCodeTemplate"	=> '<iframe src="'.$this->getMediaItemEmbedUri($mediaItem).'" width="{w}" height="{h}" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>',
			"facebookShareUri"	=> Facebook::getShareUri($this->getMediaItemUri($mediaItem)),
			"twitterShareUri"	=> "https://twitter.com/share?url=".urlencode($this->getMediaItemUri($mediaItem))."&text=".urlencode($mediaItem->name)."&via=".urlencode("LA1TV")
		);
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
	//						it's scheduled publish time is not too old or coming up (configured in config), or one of the following is true
	//						it contains an active media item.
	public function scopeActive($q) {
		$startTime = Carbon::now()->subDays(Config::get("custom.num_days_active"));
		$endTime = Carbon::now()->addDays(Config::get("custom.num_days_future_before_active"));
		return $q->accessible()->where(function($q2) use (&$startTime, &$endTime) {
			$q2->where(function($q3) use (&$startTime, &$endTime) {
				$q3->where("scheduled_publish_time", ">=", $startTime)
					->where("scheduled_publish_time", "<", $endTime);
			})->orWhereHas("mediaItems", function($q3) {
				$q3->accessible()->active();
			});
		});
	}
	
	public function scopeBelongsToShow($q, $yes=true) {
		return $q->has("show", $yes ? "!=" : "=", 0);
	}
	
	// get the cover art for the playlist or the default if there isn't one set
	public function getCoverArtUri($width, $height) {
		$coverArtFile = $this->coverArtFile;
		if (!is_null($coverArtFile)) {
			$coverArtImageFile = $coverArtFile->getImageFileWithResolution($width, $height);
			if (!is_null($coverArtImageFile) && $coverArtFile->getShouldBeAccessible()) {
				return $coverArtImageFile->getUri();
			}
		}
		// return default cover
		return Config::get("custom.default_cover_uri");
	}
	
	// get the uri that should be used as the media items cover art.
	// if the media item has one it returns that, otherwise it returns the playlist one if it has one
	// a special case is if this playlist is not a series playlist, and this media item is also in a series playlist
	// if this is the case the cover art that would be assigned to this in the series playlist will be retrieved
	// if there isn't one it returns the uri to the default cover
	public function getMediaItemCoverArtUri($mediaItem, $width, $height) {
		$mediaItemTmp = $this->mediaItems()->find($mediaItem->id);
		if (is_null($mediaItemTmp)) {
			throw(new Exception("The media item is not part of the playlist."));
		}

		// check on media item
		$coverArtFile = $mediaItem->coverArtFile;
		if (!is_null($coverArtFile)) {
			$coverArtImageFile = $coverArtFile->getImageFileWithResolution($width, $height);
			if (!is_null($coverArtImageFile) && $coverArtFile->getShouldBeAccessible()) {
				return $coverArtImageFile->getUri();
			}
		}

		// the playlist to retrieve the cover art from (because there's none assigned to the media item)
		$playlist = $this;
		// if this is a standard playlist, ie not a series playlist that belongs to a show
		// then get the artwork that would be applied to this item in its default series playlist (if there is one)
		if (is_null($mediaItem->show)) {
			$seriesPlaylist = $mediaItem->getDefaultPlaylist(true, true);
			if (!is_null($seriesPlaylist)) {
				// take cover art from the series playlist instead
				$playlist = $seriesPlaylist;
			}
		}

		// get the one on the playlist (or default)
		return $playlist->getCoverArtUri($width, $height);
	}
	
	// get the side banner image for the playlist or null if there isn't one
	public function getSideBannerUri($width, $height) {
		$sideBannerFile = $this->sideBannerFile;
		if (!is_null($sideBannerFile)) {
			$sideBannerImageFile = $sideBannerFile->getImageFileWithResolution($width, $height);
			if (!is_null($sideBannerFile) && $sideBannerFile->getShouldBeAccessible()) {
				return $sideBannerImageFile->getUri();
			}
		}
		return null;
	}
	
	// get the uri that should be used for the media item side banners.
	// if the media item has one it returns that, otherwise it returns the playlist one if it has one
	// if there isn't one it returns null
	public function getMediaItemSideBannerUri($mediaItem, $width, $height) {
		$mediaItemTmp = $this->mediaItems()->find($mediaItem->id);
		if (is_null($mediaItemTmp)) {
			throw(new Exception("The media item is not part of the playlist."));
		}
		
		// check on media item
		$sideBannerFile = $mediaItem->sideBannerFile;
		if (!is_null($sideBannerFile)) {
			$sideBannerImageFile = $sideBannerFile->getImageFileWithResolution($width, $height);
			if (!is_null($sideBannerFile) && $sideBannerFile->getShouldBeAccessible()) {
				return $sideBannerImageFile->getUri();
			}
		}
		return $this->getSideBannerUri($width, $height);
	}
	
	// get the side banner fill image for the playlist or null if there isn't one
	public function getSideBannerFillUri($width, $height) {
		$sideBannerFillFile = $this->sideBannerFillFile;
		if (!is_null($sideBannerFillFile)) {
			$sideBannerFillImageFile = $sideBannerFillFile->getImageFileWithResolution($width, $height);
			if (!is_null($sideBannerFillFile) && $sideBannerFillFile->getShouldBeAccessible()) {
				return $sideBannerFillImageFile->getUri();
			}
		}
		return null;
	}
	
	// get the uri that should be used for the media item side banner fills.
	// if the media item has one it returns that, otherwise it returns the playlist one if it has one
	// if there isn't one it returns null
	public function getMediaItemSideBannerFillUri($mediaItem, $width, $height) {
		$mediaItemTmp = $this->mediaItems()->find($mediaItem->id);
		if (is_null($mediaItemTmp)) {
			throw(new Exception("The media item is not part of the playlist."));
		}
		
		// check on media item
		$sideBannerFillFile = $mediaItem->sideBannerFillFile;
		if (!is_null($sideBannerFillFile)) {
			$sideBannerFillImageFile = $sideBannerFillFile->getImageFileWithResolution($width, $height);
			if (!is_null($sideBannerFillFile) && $sideBannerFillFile->getShouldBeAccessible()) {
				return $sideBannerFillImageFile->getUri();
			}
		}
		return $this->getSideBannerFillUri($width, $height);
	}
	
	public function getCoverUri($width, $height) {
		$coverFile = $this->coverFile;
		if (!is_null($coverFile)) {
			$coverFileImageFile = $coverFile->getImageFileWithResolution($width, $height);
			if (!is_null($coverFileImageFile) && $coverFileImageFile->getShouldBeAccessible()) {
				return $coverFileImageFile->getUri();
			}
		}
		return null;
	}
	
	// get the uri that should be used for the media item cover.
	// if the media item has one it returns that, otherwise it returns the playlist one if it has one
	// if there isn't one it returns null
	public function getMediaItemCoverUri($mediaItem, $width, $height) {
		$mediaItemTmp = $this->mediaItems()->find($mediaItem->id);
		if (is_null($mediaItemTmp)) {
			throw(new Exception("The media item is not part of the playlist."));
		}
		
		// check on media item
		$coverFile = $mediaItem->coverFile;
		if (!is_null($coverFile)) {
			$coverFileImageFile = $coverFile->getImageFileWithResolution($width, $height);
			if (!is_null($coverFileImageFile) && $coverFileImageFile->getShouldBeAccessible()) {
				return $coverFileImageFile->getUri();
			}
		}
		return $this->getCoverUri($width, $height);
	}
	
	// returns true if this playlist should be accessible now.
	// see getIsAccessibleToPublic() to determine if the public should have access
	public function getIsAccessible() {
		if (!$this->enabled) {
			return false;
		}
		if (!is_null($this->show)) {
			if (!$this->show->enabled) {
				return false;
			}
		}
		
		$sideBannerFile = $this->sideBannerFile;
		if (!is_null($sideBannerFile) && !$sideBannerFile->getFinishedProcessing()) {
			return false;
		}
		$sideBannerFillFile = $this->sideBannerFillFile;
		if (!is_null($sideBannerFillFile) && !$sideBannerFillFile->getFinishedProcessing()) {
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
		return $q->where(function($q) {
			$q->where("enabled", true)->where(function($q2) {
				$q2->has("show", "=", 0)
				->orWhereHas("show", function($q3) {
					$q3->accessible();
				});
			})->where(function($q2) {
				$q2->has("sideBannerFile", "=", 0)
				->orWhereHas("sideBannerFile", function($q3) {
					$q3->finishedProcessing();
				});
			})->where(function($q2) {
				$q2->has("sideBannerFillFile", "=", 0)
				->orWhereHas("sideBannerFillFile", function($q3) {
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
		});
	}

	public function scopeNotAccessible($q) {
		return $q->where(function($q) {
			$q->where("enabled", false)->orWhere(function($q2) {
				$q2->has("show", ">", 0)
				->whereHas("show", function($q3) {
					$q3->notAccessible();
				});
			})->orWhere(function($q2) {
				$q2->has("sideBannerFile", ">", 0)
				->whereHas("sideBannerFile", function($q3) {
					$q3->finishedProcessing(false);
				});
			})->orWhere(function($q2) {
				$q2->has("sideBannerFillFile", ">", 0)
				->whereHas("sideBannerFillFile", function($q3) {
					$q3->finishedProcessing(false);
				});
			})->orWhere(function($q2) {
				$q2->has("coverFile", ">", 0)
				->whereHas("coverFile", function($q3) {
					$q3->finishedProcessing(false);
				});
			})->orWhere(function($q2) {
				$q2->has("coverArtFile", ">", 0)
				->whereHas("coverArtFile", function($q3) {
					$q3->finishedProcessing(false);
				});
			});
		});
	}
	
	// returns true if this playlist should be accessible to the public.
	public function getIsAccessibleToPublic() {
		if (!$this->getIsAccessible()) {
			return false;
		}
		$scheduledPublishTime = $this->scheduled_publish_time;
		return is_null($scheduledPublishTime) || $scheduledPublishTime->isPast();
	}
	
	public function scopeAccessibleToPublic($q) {
		return $q->accessible()->where("scheduled_publish_time", "<=", Carbon::now());
	}

	public function scopeNotAccessibleToPublic($q) {
		return $q->where(function($q) {
			$q->notAccessible()->orWhere("scheduled_publish_time", ">", Carbon::now());
		});
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}

	public function scopeNeedsReindexing($q) {
		return $q->whereRaw("`playlists`.`pending_search_index_version` != `playlists`.`current_search_index_version`");
	}

	public function scopeUpToDateInIndex($q) {
		return $q->whereRaw("`playlists`.`pending_search_index_version` = `playlists`.`current_search_index_version`");
	}
}