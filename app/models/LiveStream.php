<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class LiveStream extends Eloquent {

	protected $table = 'live_streams';
	protected $fillable = array('name', 'description', 'load_balancer_server_address', 'server_address', 'dvr_enabled');
	
	public function qualities() {
		return $this->hasMany('LiveStreamQuality');
	}

	public function scopeUsingLoadBalancer($q, $yes) {
		return $q->where('load_balancer_server_address', $yes ? 'IS NOT' : 'IS', DB::raw('NULL'))
	}	
}