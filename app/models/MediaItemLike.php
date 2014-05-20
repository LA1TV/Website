<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItemLike extends Eloquent {

	protected $table = 'media_items_likes';
	protected $fillable = array('is_like');
	
	public function mediaItem() {
		return $this->belongsTo('MediaItem', 'media_item_id');
	}
	
	public function siteUser() {
		return $this->belongsTo('SiteUser', 'site_user_id');
	}
}