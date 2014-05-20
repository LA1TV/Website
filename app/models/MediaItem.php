<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItem extends Eloquent {

	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'cover_file_id', 'side_banner_file_id', 'enabled');
	
	public function comments() {
		return $this->hasMany('MediaItemComment');
	}

	public function likes() {
		return $this->hasMany('MediaItemLike');
	}
	
	public function liveStream() {
		return $this->hasOne('MediaItemLiveStream');
	}
	
	public function video() {
		return $this->hasOne('MediaItemVideo');
	}
}