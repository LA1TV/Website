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
	
	public function getQualitiesContent() {
		$data = array();
		$items = $this->qualities()->orderBy("position", "asc")->get();
		foreach($items as $a) {
			$data[] = array(
				"id"		=> intval($a->id),
				"name"		=> $a->name
			);
		}
		return $data;
	}
	
	public function getQualitiesForInputAttribute() {
		$ids = array();
		foreach($this->getPlaylistContent() as $a) {
			$ids[] = $a['id'];
		}
		return json_encode($ids);
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
}