<?php namespace uk\co\la1tv\website\models;

use Exception;
use Config;
use Cache;
use uk\co\la1tv\website\helpers\reorderableList\StreamUrlsReorderableList;

class LiveStream extends MyEloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'enabled');
	protected $appends = array('urls_for_orderable_list', 'urls_for_input');
	
	public function liveStreamItems() {
		return $this->hasMany(self::$p.'MediaItemLiveStream', 'live_stream_id');
	}
	
	public function liveStreamUris() {
		return $this->hasMany(self::$p.'LiveStreamUri', 'live_stream_id');
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
				"enabled"	=> (boolean) $a->enabled
			);
			
			$qualities[array_search($qualityDefinitionId, $addedQualityIds, true)]["uris"][] = $uri;
		}
		return $qualities;
	}
	
	public function getUrlsDataForReorderableList() {
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
	
	public function getQualitiesWithUris($dvrUrisOnly=false) {
		
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
				$uri = !$uriWithDvrSupport ? $uriAndInfo['uri'] : $uriAndInfo['uriFromDvrBridgeService'];
				if (is_null($uri)) {
					continue;
				}
				
				if ($dvrUrisOnly && !$uriWithDvrSupport) {
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
	
	// returns an array containing all the domains that live streams come from which are loaded from a http request. I.e. playlist.m3u8 for mobiles.
	public static function getCachedLiveStreamDomains() {
		return Cache::remember('liveStreamDomains', Config::get("custom.live_stream_domains_cache_time"), function() {
			$uris = array();
			$models = self::get();
			foreach($models as $a) {
				foreach($a->getQualitiesWithUris() as $b) {
					foreach($b['uris'] as $uri) {
						$uris[] = $uri['uri'];
					}
				}
			}
			
			$uris = array_where($uris, function($key, $value) {
				// filter out so we only have uris beginning with http:// or https://
				return preg_match('@^https?://@i', $value) === 1;
			});
			$domains = array();
			foreach($uris as $a) {
				$info = parse_url($a);
				$domain = $info['scheme']."://".$info['host'];
				if (isset($info['port'])) {
					$domain .= ":".$info['port'];
				}
				$domains[] = $domain;
			}
			$domains = array_unique($domains);
			return $domains;
		});
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
