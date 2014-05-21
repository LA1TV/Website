<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class LiveStream extends Eloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'load_balancer_server_address', 'server_address', 'dvr_enabled');
	
	public function __construct(array $attributes = array()) {
		parent::__construct($attributes);
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
	
	public function qualities() {
		return $this->hasMany('LiveStreamQuality', 'live_stream_id');
	}

	public function scopeUsingLoadBalancer($q, $yes) {
		return $q->where('load_balancer_server_address', $yes ? 'IS NOT' : 'IS', DB::raw('NULL'))
	}
	
	public function liveStreamItems() {
		return $this->hasMany('MediaItemVideoStream', 'live_stream_id');
	}
}