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
			
			$uri = array(
				"uri"	=> $a->uri,
				"uriForDvrBridgeService"	=> (boolean) $a->dvr_bridge_service_uri,
				"uriFromDvrBridgeService"	=> $a->uri_from_dvr_bridge_service,
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
	
	// $filter can be "all", "dvr", "live"
	public function getQualitiesWithUris($filter="all", $mediaItemLiveStream=null) {
		if ($filter !== "all" && $filter !== "dvr" && $filter !== "live") {
			throw(new Exception("Filter is not valid."));
		}
		
		if (($filter === "all" || $filter === "dvr") && is_null($mediaItemLiveStream)) {
			throw("MediaItemLiveStream model required if retrieving dvr urls.");
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
				$uriWithDvrSupport = $uriAndInfo['uriForDvrBridgeService'];
				// if a dvr bridge service is being used then the url it provides will be placed in uri_from_dvr_bridge_service
				// this may be null if there's been an error, in which case the user should not see it
				$uri = null;
				if(!$uriWithDvrSupport) {
					$uri = $uriAndInfo['uri'];
				}
				else if ($filter === "all" || $filter === "dvr") {
					$dvrLiveStreamUriModel = $uriAndInfo["liveStreamUriModel"]->dvrLiveStreamUris()->where("dvr_live_stream_uris.media_item_live_stream_id", $mediaItemLiveStream->id)->first();
					if (!is_null($dvrLiveStreamUriModel)) {
						$uri = $dvrLiveStreamUriModel->uri;
					}
				}
				
				if (is_null($uri)) {
					continue;
				}
				
				if (($filter === "dvr" && !$uriWithDvrSupport) || ($filter === "live" && $uriWithDvrSupport)) {
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
					"dvr"		=> $b['uriForDvrBridgeService'],
					"type"		=> $b['type'],
					"support"	=> $support
				);
			}
		}
		return $urls;
	}
	
	public function registerWatching() {
		if (!$this->getIsAccessible() || !$this->getShowAsLiveStream()) {
			return false;
		}
		
		// delete any entries that have expired.
		$cutOffTime = Carbon::now()->subSeconds(30);
		LiveStreamWatchingNow::where("updated_at", "<", $cutOffTime)->delete();
		
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
