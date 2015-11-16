<?php namespace uk\co\la1tv\website\models;

use Exception;
use Config;
use Cache;
use Carbon;
use URL;
use Session;
use DB;
use Facebook;
use uk\co\la1tv\website\helpers\reorderableList\StreamUrlsReorderableList;

class LiveStream extends MyEloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'enabled', 'shown_as_livestream');
	protected $appends = array('urls_for_orderable_list', 'urls_for_input');
	
	public function liveStreamItems() {
		return $this->hasMany(self::$p.'MediaItemLiveStream', 'live_stream_id');
	}
	
	public function liveStreamUris() {
		return $this->hasMany(self::$p.'LiveStreamUri', 'live_stream_id');
	}

	public function coverArtFile() {
		return $this->belongsTo(self::$p.'File', 'cover_art_file_id');
	}

	public function watchingNows() {
		return $this->hasMany(self::$p.'LiveStreamWatchingNow', 'live_stream_id');
	}
	
	public function getNumWatchingNow() {
		$cutOffTime = Carbon::now()->subSeconds(30);
		return $this->watchingNows()->where("updated_at", ">", $cutOffTime)->count();
	}
	
	private function getUrisOrganisedByQuality() {
		$this->load("liveStreamUris", "liveStreamUris.qualityDefinition");
		
		$addedQualityIds = array();
		$addedQualityPositions = array();
		$qualities = array();
		foreach($this->liveStreamUris as $a) {
			
			$qualityDefinition = $a->qualityDefinition;
			$qualityDefinitionId = intval($qualityDefinition->id);
			
			if (!in_array($qualityDefinitionId, $addedQualityIds)) {
				$addedQualityIds[] = $qualityDefinitionId;
				$addedQualityPositions[] = intval($qualityDefinition->position);
				$qualities[] = array(
					"qualityDefinition"	=> $qualityDefinition,
					"uris"				=> array()
				);
			}

			$uriForDvrBridgeService = (boolean) $a->dvr_bridge_service_uri;
			
			$uri = array(
				"uri"	=> $a->uri,
				"uriForDvrBridgeService"	=> $uriForDvrBridgeService,
				"hasDvr"	=> $uriForDvrBridgeService ? null : (boolean) $a->has_dvr,
				"type"	=> $a->type,
				"supportedDevices"	=> $a->supported_devices,
				"enabled"	=> (boolean) $a->enabled,
				"liveStreamUriModel"	=> $a
			);
			
			$qualities[array_search($qualityDefinitionId, $addedQualityIds, true)]["uris"][] = $uri;
		}
		// reorder so in qualities order
		array_multisort($addedQualityPositions, SORT_NUMERIC, SORT_ASC, $qualities);
		return $qualities;
	}
	
	// $filters is array of "dvrBridge", "nativeDvr", or "live"
	// "dvrBridge" will return all urls which have been created by the dvr bridge service for the provided $mediaItemLiveStream
	// "nativeDvr" will return all urls which point to streams which provide dvr functionality
	// "live" will return all urls which point to streams which do not provide dvr functionality
	public function getQualitiesWithUris($filters=null, $mediaItemLiveStream=null) {
		$validFilters = array("dvrBridge", "nativeDvr", "live");
		if (is_null($filters)) {
			$filters = array("dvrBridge", "live");
		}

		if (!is_array($filters)) {
			throw new Exception("filters must be an array.");
		}

		foreach($filters as $a) {
			if (!in_array($a, $validFilters)) {
				throw new Exception("Unrecognised filter.");
			}
		}

		if (in_array("dvrBridge", $filters) && is_null($mediaItemLiveStream)) {
			throw new Exception("MediaItemLiveStream model required if retrieving dvr bridge service urls.");
		}
		
		$qualities = array();
		$urisOrganisedByQuality = $this->getUrisOrganisedByQuality();
		foreach($urisOrganisedByQuality as $quality) {

			$entry = array(
				"qualityDefinition"	=> $quality["qualityDefinition"],
				"uris"				=> array()
			);
			
			foreach($quality['uris'] as $uriAndInfo) {
				if (!$uriAndInfo['enabled']) {
					continue;
				}
				
				// if the url is a url for a dvr bridge service then the url the user gets will be the url it returns
				// the url it returns will be a hls url with dvr support.
				$uriForDvrBridgeService = $uriAndInfo['uriForDvrBridgeService'];
				$uriWithDvrSupport = $uriForDvrBridgeService || $uriAndInfo["hasDvr"];
				// if a dvr bridge service is being used then the url it provides will be placed in uri_from_dvr_bridge_service
				// this may be null if there's been an error, in which case the user should not see it
				$uri = null;
				if(!$uriForDvrBridgeService) {
					if (in_array("nativeDvr", $filters) && $uriWithDvrSupport) {
						$uri = $uriAndInfo['uri'];
					}
					else if (in_array("live", $filters) && !$uriWithDvrSupport) {
						$uri = $uriAndInfo['uri'];
					}
				}
				else {
					if (in_array("dvrBridge", $filters)) {
						$dvrLiveStreamUriModel = $uriAndInfo["liveStreamUriModel"]->dvrLiveStreamUris()->where("dvr_live_stream_uris.media_item_live_stream_id", intval($mediaItemLiveStream->id))->first();
						if (!is_null($dvrLiveStreamUriModel)) {
							$uri = $dvrLiveStreamUriModel->uri;
						}
					}
				}
				
				if (is_null($uri)) {
					continue;
				}
				
				$entry['uris'][] = array(
					"uri"	=> $uri,
					"uriWithDvrSupport"	=> $uriWithDvrSupport,
					"type"	=> $uriAndInfo['type'],
					"supportedDevices"	=> $uriAndInfo['supportedDevices']
				);
			}
			
			if (count($entry["uris"]) > 0) {
				$qualities[] = $entry;
			}
		}
		return $qualities;
	}
	
	public function getUrlsDataForReorderableList() {
		$urls = array();
		$urisOrganisedByQuality = $this->getUrisOrganisedByQuality();
		foreach($urisOrganisedByQuality as $a) {
			foreach($a['uris'] as $b) {
				$supportedDevices = is_null($b['supportedDevices']) ? array() : explode(",", $b['supportedDevices']);
				$support = "all";
				if (!$b['enabled']) {
					$support = "none";
				}
				else if (in_array("desktop", $supportedDevices, true)) {
					$support = "pc";
				}
				else if (in_array("mobile", $supportedDevices, true)) {
					$support = "mobile";
				}
				$urls[] = array(
					"qualityState"	=> array(
						"id"	=> intval($a['qualityDefinition']->id),
						"text"	=> $a['qualityDefinition']->name
					),
					"url"		=> $b['uri'],
					"dvrBridgeServiceUrl"	=> $b['uriForDvrBridgeService'],
					"nativeDvr"	=> $b['hasDvr'],
					"type"		=> $b['type'],
					"support"	=> $support
				);
			}
		}
		return $urls;
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
	
	// $playing is true if the content is currently playing
	public function registerWatching($playing) {
		if (!$this->getIsAccessible() || !$this->getShowAsLiveStream()) {
			return false;
		}
		
		// delete any entries that have expired.
		$cutOffTime = Carbon::now()->subSeconds(30);
		LiveStreamWatchingNow::where("updated_at", "<", $cutOffTime)->delete();
		
		if ($playing) {
			DB::transaction(function() {
				$sessionId = Session::getId();
				$model = LiveStreamWatchingNow::where("session_id", $sessionId)->where("live_stream_id", $this->id)->first();
				if (is_null($model)) {
					$model = new LiveStreamWatchingNow(array(
						"session_id"	=> $sessionId
					));
					$model->liveStream()->associate($this);
					$model->save();
				}
				else {
					$model->touch();
				}
			});
		}

		// determine what media item is currently live on the stream
		// and if there is one register the user as watching that as well
		$liveMediaItem = $this->getLiveMediaItem();
		if (!is_null($liveMediaItem)) {
			$liveMediaItem->registerWatching($playing, null);
		}
		return true;
	}

	public function getUrlsForOrderableListAttribute() {
		return self::generateInitialDataForUrlsOrderableList($this->getUrlsDataForReorderableList());
	}
	
	public function getUrlsForInputAttribute() {
		return self::generateInputValueForUrlsOrderableList($this->getUrlsDataForReorderableList());
	}
	
	public static function isValidDataFromUrlsOrderableList($data) {
		$reorderableList = new StreamUrlsReorderableList($data);
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForUrlsOrderableList($data) {
		$reorderableList = new StreamUrlsReorderableList($data);
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForUrlsOrderableList($data) {
		$reorderableList = new StreamUrlsReorderableList($data);
		return $reorderableList->getStringForInput();
	}
	
	public static function getCachedSiteLiveStreams() {
		return Cache::remember('siteLiveStreams', Config::get("custom.cache_time"), function() {
			return self::showAsLiveStream()->orderBy("name", "asc")->get();
		});
	}

	// returns true if this should be shown as a livestream on the site
	public function getShowAsLiveStream() {
		return $this->shown_as_livestream;
	}

	public function scopeShowAsLivestream($q) {
		return $q->where("shown_as_livestream", true);
	}

	public function getUri() {
		return URL::route('liveStream', array($this->id));
	}

	public function getEmbedUri() {
		return URL::route('embed-player-live-stream', array($this->id));
	}

	public function getEmbedData() {
		return array(
			"embedCodeTemplate"	=> '<iframe src="'.$this->getEmbedUri().'" width="{w}" height="{h}" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>',
			"facebookShareUri"	=> Facebook::getShareUri($this->getUri()),
			"twitterShareUri"	=> "https://twitter.com/share?url=".urlencode($this->getEmbedUri())."&text=".urlencode($this->name)."&via=".urlencode("LA1TV")
		);
	}

	// get the MediaItem that is live on this stream at the moment
	public function getLiveMediaItem() {
		// there may be more than one media item live stream which is live at the same time
		// this will just pick the one scheduled later which should be consistant
		// if there is ever more than one media item live at once it would probably only be
		// for a short period of time anyway during a switch over
		return MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
			$q->accessible()->live()->whereHas("livestream", function($q2) {
				$q2->accessible()->where("id", intval($this->id));
			});
		})->orderBy("scheduled_publish_time", "desc")->first();
	}

	// get the MediaItem that was last on this stream
	public function getPreviouslyLiveMediaItem() {
		return MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
			$q->accessible()->showOver()->whereHas("livestream", function($q2) {
				// the live stream doesn't actually have to be accessible right now
				// the assumption is that it was when the item was live
				$q2->where("id", intval($this->id));
			});
		})->where("scheduled_publish_time", "<=", Carbon::now())->orderBy("scheduled_publish_time", "desc")->first();
	}

	// get the MediaItem that will be next on this stream
	public function getComingUpMediaItem() {
		return MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
			$q->accessible()->notLive()->whereHas("livestream", function($q2) {
				// the live stream doesn't actually have to be accessible right now
				// the assumption is that it will be at the time this is due to go live
				$q2->where("id", intval($this->id));
			});
		})->orderBy("scheduled_publish_time", "desc")->first();
	}

	public function getIsAccessible() {
		return $this->enabled && $this->liveStreamUris()->count() > 0;
	}

	public function scopeAccessible($q) {
		return $q->where("enabled", true)->has("liveStreamUris", ">", 0);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function isDeletable() {
		return !$this->enabled && $this->liveStreamItems()->count() === 0;
	}
}
