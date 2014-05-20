<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItem extends Eloquent {

	protected $table = 'media_items';
	protected $fillable = array('name', 'description', 'cover_file_id', 'side_banner_file_id', 'enabled');
	
	public function comments() {
		return $this->hasMany('MediaItemComment', 'media_item_id');
	}

	public function likes() {
		return $this->hasMany('MediaItemLike', 'media_item_id');
	}
	
	public function liveStreamItem() {
		return $this->hasOne('MediaItemLiveStream', 'media_item_id');
	}
	
	public function videoItem() {
		return $this->hasOne('MediaItemVideo', 'media_item_id');
	}
	
	public function sideBannerFile() {
		return $this->hasOne('file', 'side_banner_file_id');
	}
	
	public function coverFile() {
		return $this->hasOne('file', 'cover_file_id');
	}
}