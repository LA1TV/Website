<?php namespace uk\co\la1tv\website\models;

use Exception;
use Config;
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
	
	public function getUrisOrganisedByQuality() {
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
