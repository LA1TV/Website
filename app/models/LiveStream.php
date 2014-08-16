<?php namespace uk\co\la1tv\website\models;

class LiveStream extends MyEloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'load_balancer_server_address', 'server_address', 'dvr_enabled', 'stream_name', 'enabled');
	
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
		return $this->hasMany(self::$p.'MediaItemVideoStream', 'live_stream_id');
	}
	
	public function qualities() {
		return $this->belongsToMany(self::$p.'LiveStreamQuality', 'live_stream_qualitiy_to_live_stream', 'live_stream_id', 'live_stream_quality_id');
	}
	
	// should be the string from the input
	public static function generateQualitiesForOrderableList($stringFromInput) {
		$data = json_decode($stringFromInput, true);
		if (!is_array($data)) {
			return "[]";
		}
		$output = array();
		$ids = array();
		foreach($data as $a) {
			if (is_int($a) && !in_array($a, $ids, true)) {
				$ids[] = $a;
			}
			$output[] = array(
				"id"	=> is_int($a) ? $a : null,
				"text"	=> null
			);
		}
		if (count($ids) > 0) {
			$qualities = LiveStreamQuality::with("qualityDefinition")->whereIn("id", $ids)->get();
			$qualityIds = array();
			foreach($qualities as $a) {
				$qualityIds[] = intval($a->id);
			}
			foreach($output as $i=>$a) {
				if (is_null($a['id'])) {
					continue;
				}
				$qualityIndex = array_search($a['id'], $qualityIds, true);
				if ($qualityIndex === false) {
					$output[$i]["id"] = null; // if the quality can't be found anymore make the id null as well.
					continue;
				}
				$output[$i]["text"] = $qualities[$qualityIndex]->qualityDefinition->name;
			}
		}
		return json_encode($output);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}