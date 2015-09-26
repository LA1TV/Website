<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;
use uk\co\la1tv\website\helpers\reorderableList\MediaItemCreditsReorderableList;
use FormHelpers;
use Carbon;
use Exception;
use Config;
use DB;
use Cache;
use URL;
use Session;

class MediaItem extends MyEloquent {
	
	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'enabled', 'scheduled_publish_time', 'email_notifications_enabled', 'likes_enabled', 'comments_enabled');
	protected $appends = array("related_items_for_reorderable_list", "related_items_for_input", "credits_for_reorderable_list", "credits_for_input", "scheduled_publish_time_for_input");
	
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
	
	public function sideBannerFillFile() {
		return $this->belongsTo(self::$p.'File', 'side_banner_fill_file_id');
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
	
	public function playlistsRelatedTo() {
		return $this->belongsToMany(self::$p.'Playlist', 'related_item_to_playlist', 'related_media_item_id', 'media_item_id')->withPivot('position');
	}
	
	public function emailTasksMediaItem() {
		return $this->hasMany(self::$p.'EmailTasksMediaItem', 'media_item_id');
	}
	
	public function credits() {
		return $this->morphMany('uk\co\la1tv\website\models\Credit', 'creditable');
	}
	
	public function watchingNows() {
		return $this->hasMany(self::$p.'WatchingNow', 'media_item_id');
	}
	
	public function getNumWatchingNow() {
		$cutOffTime = Carbon::now()->subSeconds(30);
		return $this->watchingNows()->where("updated_at", ">", $cutOffTime)->where("playing", true)->count();
	}
	
	private function getRelatedItemIdsForReorderableList() {
		$ids = array();
		$items = $this->relatedItems()->orderBy("related_item_to_media_item.position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getRelatedItemsForReorderableListAttribute() {
		return self::generateInitialDataForAjaxSelectReorderableList($this->getRelatedItemIdsForReorderableList());
	}
	
	public function getRelatedItemsForInputAttribute() {
		return self::generateInputValueForAjaxSelectReorderableList($this->getRelatedItemIdsForReorderableList());
	}
	
	private function getCreditsDataForReorderableList() {
		$positions = array();
		$names = array();
		$data = array();
		$this->load("credits", "credits.productionRole", "credits.siteUser", "credits.productionRole.productionRoleMediaItem");
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
		return self::generateInitialDataForMediaItemCreditsReorderableList($this->getCreditsDataForReorderableList());
	}
	
	public function getCreditsForInputAttribute() {
		return self::generateInputValueForMediaItemCreditsReorderableList($this->getCreditsDataForReorderableList());
	}
	
	public static function isValidIdsFromAjaxSelectReorderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForAjaxSelectReorderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->getNameWithInfo();
		});
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForAjaxSelectReorderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new MediaItem();
		}, function($model) {
			return $model->name;
		});
		return $reorderableList->getStringForInput();
	}
	
	public static function isValidDataFromMediaItemCreditsReorderableList($data) {
		$reorderableList = new MediaItemCreditsReorderableList($data);
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForMediaItemCreditsReorderableList($data) {
		$reorderableList = new MediaItemCreditsReorderableList($data);
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForMediaItemCreditsReorderableList($data) {
		$reorderableList = new MediaItemCreditsReorderableList($data);
		return $reorderableList->getStringForInput();
	}
	
	public function getScheduledPublishTimeForInputAttribute() {
		if (is_null($this->scheduled_publish_time)) {
			return null;
		}
		return FormHelpers::formatDateForInput($this->scheduled_publish_time->timestamp);
	}
	
	public function getNameWithInfo() {
		$text = $this->name;
		if (!is_null($this->description)) {
			$text .= " (".str_limit($this->description, 60, '...').")";
		}
		$names = array();
		foreach($this->playlists as $playlist) {
			$names[] = $playlist->generateName();
		}
		if (count($names) > 0) {
			$text .= ' (In "'.implode('", "', $names).'")';
		}
		return $text;
	}
	
	// $playing is true if the video is currently playing
	public function registerWatching($playing) {
		if (!(
			$this->getIsAccessible() &&
			(!is_null($this->liveStreamItem) && ($this->liveStreamItem->hasWatchableContent())) ||
			(!is_null($this->videoItem) && $this->videoItem->getIsLive())
		)) {
			// there is nothing that can be watched
			return false;
		}
		
		// delete any entries that have expired.
		$intervalBetweenViewCounts = Config::get("custom.interval_between_registering_view_counts") * 60;
		$expireDuration = max(30, $intervalBetweenViewCounts);
		$cutOffTime = Carbon::now()->subSeconds($expireDuration);
		WatchingNow::where("updated_at", "<", $cutOffTime)->delete();

		DB::transaction(function() use (&$playing, &$intervalBetweenViewCounts) {
			$sessionId = Session::getId();
			$model = WatchingNow::where("session_id", $sessionId)->where("media_item_id", intval($this->id))->first();
			if (is_null($model)) {
				$model = new WatchingNow(array(
					"session_id"	=> $sessionId,
					"playing"		=> $playing
				));
				$model->mediaItem()->associate($this);
				if ($playing) {
					$this->registerView();
				}
			}
			else {
				$lastPlayTime = $model->last_play_time;
				$now = Carbon::now();
				
				if ($playing) {
					// this is the last time the content was reported as playing (by any tab)
					$model->last_play_time = $now;
				}

				if (!$playing && !is_null($lastPlayTime) && $lastPlayTime->timestamp >= $now->timestamp - 30) {
					// there was a play reported recently.
					// assume the content is still playing
					// could be a different browser tab with the content paused which made this request
					$playing = true;
				}

				if ($playing && !$model->playing) {
					// register as a view if $intervalBetweenViewCounts has passed since last play
					if (is_null($lastPlayTime) || $lastPlayTime->timestamp < $now->timestamp - $intervalBetweenViewCounts) {
						$this->registerView();
					}
				}

				$model->playing = $playing;
			}
			$model->save();
		});
		return true;
	}
	
	public function registerView() {
		$liveStreamItem = $this->liveStreamItem;
		$videoItem = $this->videoItem;
		// try registering the view with the live stream first
		if (is_null($liveStreamItem) || !$liveStreamItem->registerView()) {
			// live stream wouldn't accept view so assign to video
			if (!is_null($videoItem)) {
				$videoItem->registerView();
			}
		}
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
	public function getDefaultPlaylist($accessibleToPublic=true, $restrictToSeriesPlaylists=false) {
		$playlist = null;
		$models = $this->playlists();
		if ($accessibleToPublic) {
			$models = $models->accessibleToPublic();
		}
		$models = $models->orderBy("scheduled_publish_time", "desc")->get();
		foreach($models as $a) {
			if (is_null($playlist) && !$restrictToSeriesPlaylists) {
				$playlist = $a;
			}
			
			if (!is_null($a->show)) {
				$playlist = $a;
				break;
			}
		}
		return $playlist;
	}
	
	public function getEmbedUri() {
		return URL::route('embed-player-media-item', array($this->id));
	}
	
	// returns an array of ("mediaItem", "generatedName")
	public static function getCachedLiveItems() {
		return Cache::remember('liveMediaItems', Config::get("custom.cache_time"), function() {
			$mediaItems = self::accessible()->orderBy("scheduled_publish_time", "desc")->orderBy("name", "asc")->whereHas("liveStreamItem", function($q) {
				$q->accessible()->live();
			})->get();
			
			$items = array();
			foreach($mediaItems as $a) {
				$playlist = $a->getDefaultPlaylist();
				$generatedName = $a->name;
				if (!is_null($playlist->show)) {
					$generatedName = $playlist->generateName() . ": " . $generatedName;
				}
				$uri = $playlist->getMediaItemUri($a);
				$items[] = array(
					"mediaItem"		=> $a,
					"generatedName"	=> $generatedName,
					"uri"			=> $uri
				);
			}
			return $items;
		});
	}
	
	public static function getCachedPromotedItems() {
		return Cache::remember('promotedMediaItems', Config::get("custom.cache_time"), function() {
			// retrieve y number of items in each direction, with items that are more than z time away excluded
			// then ordered by time away from now ascending
			// if shortage of content then most popular items will be appended to end to bring up to $numItemsToShow
			$itemTimeSpan = intval(Config::get("promoCarousel.itemTimeSpan")); // items further away than this time (seconds) should be excluded
			$numItemsEachDirection = intval(Config::get("promoCarousel.numItemsEachDirection")); // number items to find in each direction
			$numItemsToShow = intval(Config::get("promoCarousel.numItemsToShow"));
			
			$now = Carbon::now();
			$futureCutOffDate = (new Carbon($now))->addSeconds($itemTimeSpan);
			$pastCutOffDate = (new Carbon($now))->subSeconds($itemTimeSpan);
			
			$futureItems = self::with("liveStreamItem", "videoItem")->accessible()->where("scheduled_publish_time", ">=", $now)->where("scheduled_publish_time", "<", $futureCutOffDate)->where(function($q) {
				$q->has("liveStreamItem", "=", 0)
				->orWhereHas("liveStreamItem", function($q2) {
					$q2->accessible()->showOver(false);
				});
			})->orderBy("scheduled_publish_time", "asc")->take($numItemsEachDirection)->get();
			
			$pastItems = self::with("liveStreamItem", "videoItem")->accessible()->where("scheduled_publish_time", "<", $now)->where("scheduled_publish_time", ">=", $pastCutOffDate)->where(function($q) {
				$q->whereHas("videoItem", function($q2) {
					$q2->live()->whereHas("sourceFile", function($q3) {
						$q3->finishedProcessing();
					});
				})
				->orWhereHas("liveStreamItem", function($q2) {
					$q2->accessible()->where(function($q3) {
						$q3->showOver(false);
					})->orWhere(function($q3) {
						$q3->showOver(true)->hasDvrRecording(true);
					});
				});
			})->orderBy("scheduled_publish_time", "desc")->take($numItemsEachDirection)->get();
		
			$items = $pastItems->merge($futureItems);
			$distances = array();
			$finalItems = array();
			$finalItemsIds = array();
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
			foreach($items as $a) {
				$playlist = $a->getDefaultPlaylist();
				$generatedName = $playlist->generateEpisodeTitle($a);
				$uri = $playlist->getMediaItemUri($a);
				$finalItems[] = array(
					"mediaItem"		=> $a,
					"generatedName"	=> $generatedName,
					"seriesName"	=> !is_null($playlist->show) ? $playlist->generateName() : null,
					"uri"			=> $uri,
					"coverArtUri"	=> $playlist->getMediaItemCoverArtUri($a, $coverArtResolutions['full']['w'], $coverArtResolutions['full']['h'])
				);
				$finalItemIds[] = intval($a->id);
				$distances[] = abs($now->timestamp - $a->scheduled_publish_time->timestamp);
			}
			array_multisort($distances, SORT_NUMERIC, SORT_ASC, $finalItems);
			if (count($finalItems) < $numItemsToShow) {
				$popularItems = self::getCachedMostPopularItems();
				foreach($popularItems as $a) {
					$itemId = intval($a['mediaItem']->id);
					if(in_array($itemId, $finalItemsIds)) {
						// this item is already in the list
						continue;
					}
					$finalItems[] = array(
						"mediaItem"		=> $a['mediaItem'],
						"generatedName"	=> $a['generatedName'],
						"seriesName"	=> !is_null($a['playlist']->show) ? $a['playlistName'] : null,
						"uri"			=> $a['uri'],
						"coverArtUri"	=> $a['playlist']->getMediaItemCoverArtUri($a['mediaItem'], $coverArtResolutions['full']['w'], $coverArtResolutions['full']['h'])
					);
					$finalItemIds[] = $itemId;
					if (count($finalItems) === $numItemsToShow) {
						break;
					}
				}
			}
			else {
				$finalItems = array_slice($finalItems, 0, $numItemsToShow);
			}
			return $finalItems;
		});
	}
	
	public static function getCachedRecentItems() {
		return Cache::remember('recentMediaItems', Config::get("custom.cache_time"), function() {
			$numRecentItems = intval(Config::get("custom.num_recent_items"));
			$mediaItems = self::accessible()->active()->whereHas("videoItem", function($q) {
				$q->live()->whereHas("sourceFile", function($q2) {
					$q2->finishedProcessing();
				});
			})->orderBy("scheduled_publish_time", "desc")->orderBy("name", "asc")->take($numRecentItems)->get();
			
			$items = array();
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
			foreach($mediaItems as $a) {
				$playlist = $a->getDefaultPlaylist();
				$generatedName = $playlist->generateEpisodeTitle($a);
				$uri = $playlist->getMediaItemUri($a);
				
				$playlistName = $playlist->generateName();
				$items[] = array(
					"playlist"		=> $playlist,
					"mediaItem"		=> $a,
					"generatedName"	=> $generatedName,
					"playlistName"	=> $playlistName,
					"uri"			=> $uri,
					"coverArtUri"	=> $playlist->getMediaItemCoverArtUri($a, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h'])
				);
			}
			return $items;
		});
	}
	
	// the accessible media items with the most views
	public static function getCachedMostPopularItems() {
		return Cache::remember('mostPopularMediaItems', Config::get("custom.cache_time"), function() {
			$numPopularItems = intval(Config::get("custom.num_popular_items"));
			$cachedPopularIds = self::getCachedMostPopularMediaItemIds();
			
			if (count($cachedPopularIds) === 0) {
				return array();
			}
			
			$tmp = "";
			foreach($cachedPopularIds as $i=>$a) {
				if ($i > 0) {
					$tmp .= ",";
				}
				$tmp .= "'".$a."'";
			}
			$mediaItems = self::accessible()->whereIn("id", $cachedPopularIds)->orderBy(DB::raw("FIELD(id,".$tmp.")"), "asc")->orderBy("scheduled_publish_time", "desc")->orderBy("name", "asc")->take($numPopularItems)->get();
			
			$items = array();
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
			foreach($mediaItems as $a) {
				$playlist = $a->getDefaultPlaylist();
				$generatedName = $playlist->generateEpisodeTitle($a);
				$uri = $playlist->getMediaItemUri($a);
				
				$playlistName = $playlist->generateName();
				$items[] = array(
					"playlist"		=> $playlist,
					"mediaItem"		=> $a,
					"generatedName"	=> $generatedName,
					"playlistName"	=> $playlistName,
					"uri"			=> $uri,
					"coverArtUri"	=> $playlist->getMediaItemCoverArtUri($a, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h'])
				);
			}
			return $items;
		});
	}
	
	public static function getCachedMostPopularMediaItemIds() {
		return Cache::remember('mostPopularMediaItemIds', Config::get("custom.popular_items_cache_time"), function() {
			$mediaItems = self::with("liveStreamItem", "videoItem")->accessible()->where(function($q) {
				$q->whereHas("liveStreamItem", function($q2) {
					$q2->accessible();
				})
				->orWhereHas("videoItem", function($q2) {
					$q2->accessible();
				});
			})->orderBy("id", "asc")->get();
			$ids = array();
			$counts = array();
			foreach($mediaItems as $a) {
				$liveStreamItem = $a->liveStreamItem;
				$videoItem = $a->videoItem;
				$count = 0;
				if (!is_null($liveStreamItem) && $liveStreamItem->getIsAccessible()) {
					$count += intval($liveStreamItem->view_count);
				}
				if (!is_null($videoItem) && $videoItem->getIsAccessible()) {
					$count += intval($videoItem->view_count);
				}
				if ($count === 0) {
					continue;
				}
				$ids[] = $a['id'];
				$counts[] = $count;
			}
			
			array_multisort($counts, SORT_NUMERIC, SORT_DESC, $ids);
			$numPopularItemsToCache = intval(Config::get("custom.num_popular_items_to_cache"));
			$ids = array_slice($ids, 0, $numPopularItemsToCache);
			return $ids;
		});
	}	
	
	// returns true if this media item should be accessible
	// this does not take into consideration the publish time. A media item should still be accessible even if the publish time hasn't passed.
	// If the publish time hasn't passed then and there's a MediaItemVideo attached it should not be watchable until after this time.
	// same applies to a live stream (although with a live stream there is no actual restriction, the stream could start earlier/later)
	public function getIsAccessible() {
		
		if (!$this->enabled) {
			return false;
		}
		if ($this->playlists()->accessibleToPublic()->count() === 0) {
			return false;
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
		return $q->where("enabled", true)->whereHas("playlists", function($q2) {
			$q2->accessibleToPublic();
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