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
	
	public function getUrlsDataForReorderableList() {
		$qualitiesWithUris = $this->getQualitiesWithUris();
		$urls = array();
		foreach($qualitiesWithUris as $a) {
			foreach($a['uris'] as $b) {
				$supportedDevices = is_null($b['supportedDevices']) ? array() : explode(",", $b['supportedDevices']);
				$support = "all";
				if (in_array("desktop", $supportedDevices, true)) {
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
	
	
	
	public function getQualitiesWithUris() {
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
				"type"	=> $a->type,
				"supportedDevices"	=> $a->supported_devices
			);
			
			$qualities[array_search($qualityDefinitionId, $addedQualityIds, true)]["uris"][] = $uri;
		}
		// sort so qualities in correct order
		array_multisort($addedQualityPositions, SORT_NUMERIC, SORT_ASC, $qualities);
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
