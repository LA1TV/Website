<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class MediaItemComment extends Eloquent {

	protected $table = 'media_items_comments';
	protected $fillable = array('msg');
	
	public function mediaItem() {
		return $this->belongsTo('MediaItem', 'media_item_id');
	}
	
	public function siteUser() {
		return $this->belongsTo('SiteUser', 'site_user_id');
	}
}