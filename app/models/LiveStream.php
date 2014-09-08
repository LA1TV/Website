<?php namespace uk\co\la1tv\website\models;

use Exception;

class LiveStream extends MyEloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'load_balancer_server_address', 'server_address', 'dvr_enabled', 'stream_name', 'enabled');
	protected $appends = array("qualities_for_input", "qualities_for_orderable_select");
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->load_balancer_server_address === NULL && $model->server_address === NULL) {
				throw(new Exception("Either 'load_balancer_server_address' or 'server_address' must be set."));
			}
			else if ($model->load_balancer_server_address !== NULL && $model->server_address !== NULL) {
				throw(new Exception("Only one of 'load_balancer_server_address' or 'server_address' must be set."));
			}
			return true;
		});
	}
	
	public function scopeUsingLoadBalancer($q, $yes) {
		return $q->where(self::$p.'load_balancer_server_address', $yes ? 'IS NOT' : 'IS', DB::raw('NULL'));
	}
	
	public function liveStreamItems() {
		return $this->hasMany(self::$p.'MediaItemLiveStream', 'live_stream_id');
	}
	
	public function qualities() {
		return $this->belongsToMany(self::$p.'QualityDefinition', 'quality_definition_to_live_stream', 'live_stream_id', 'quality_definition_id');
	}
	
	public function getQualitiesForInputAttribute() {
		return QualityDefinition::generateInputValueForAjaxSelectOrderableList($this->getQualityIdsForOrderableList());
	}
	
	public function getQualitiesForOrderableListAttribute() {
		return QualityDefinition::generateInitialDataForAjaxSelectOrderableList($this->getQualityIdsForOrderableList());
	}
	
	private function getQualityIdsForOrderableList() {
		$ids = array();
		$this->load("qualities");
		$items = $this->qualities()->orderBy("position", "asc")->get();
		foreach($items as $a) {
			$ids[] = intval($a->id);
		}
		return $ids;
	}
	
	public function getQualitiesWithUris() {
		$this->load("qualities", "qualities.liveStreamQualities");
		
		$qualities = array();
		$positions = array();
		foreach($this->qualities()->orderBy("position", "asc")->get() as $a) {
			
			$liveStreamQualities = $a->liveStreamQualities;
			if (count($liveStreamQualities) === 0) {
				// don't show a quality entry if there are not stream uris for that quality definition
				continue;
			}
			
			$uris = array();
			foreach($liveStreamQualities as $b) {
				$uris[] = array(
					"uri"	=> $b->getBuiltUrl($this->server_address, "live", $this->stream_name),
					"type"	=> $b->type
				);
			}
			
			$qualities[] = array(
				"qualityDefinition"	=> $a,
				"uris"				=> $uris
			);
		}
		return $qualities;
	}
	
	
	public function getIsAccessible() {
		return $this->enabled && $this->qualities()->count() > 0;
	}
	
	public function scopeAccessible($q) {
		return $q->where("enabled", true)->has("qualities", ">", 0);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public function isDeletable() {
		return !$this->enabled && $this->liveStreamItems()->count() === 0;
	}
}