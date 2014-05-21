<?php namespace uk\co\la1tv\website\models;

class MediaItemLike extends MyEloquent {

	protected $table = 'media_items_likes';
	protected $fillable = array('is_like');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function siteUser() {
		return $this->belongsTo(self::$p.'SiteUser', 'site_user_id');
	}
}