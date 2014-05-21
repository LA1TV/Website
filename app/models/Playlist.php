<?php namespace uk\co\la1tv\website\models;

class Playlist extends MyEloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'is_series');
	
	public function coverFile() {
		return $this->hasOne(self::$p.'File', 'cover_file_id');
	}
	
	public function sideBannerFile() {
		return $this->hasOne(self::$p.'File', 'side_banner_file_id');
	}

	public function mediaItems() {
		return $this->belongsToMany(self::$p.'MediaItem', 'media_item_to_playlist', 'media_item_id', 'playlist_id')->withPivot('position', 'from_playlist_id');
	}
}