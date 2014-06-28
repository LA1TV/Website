<?php namespace uk\co\la1tv\website\models;

class MediaItemComment extends MyEloquent {

	protected $table = 'media_items_comments';
	protected $fillable = array('msg');
	
	public function mediaItem() {
		return $this->belongsTo(self::$p.'MediaItem', 'media_item_id');
	}
	
	public function siteUser() {
		return $this->belongsTo(self::$p.'SiteUser', 'site_user_id');
	}
}