<?php namespace uk\co\la1tv\website\models;

class File extends MyEloquent {

	protected $table = 'files';
	protected $fillable = array('in_use', 'filename', 'size', 'session_id');
	
	public function mediaItemWithCover() {
		return $this->hasOne(self::$p.'MediaItem', 'cover_file_id');
	}
	
	public function mediaItemWithBanner() {
		return $this->hasOne(self::$p.'MediaItem', 'side_banner_file_id');
	}
	
	public function playlistWithCover() {
		return $this->hasOne(self::$p.'Playlist', 'cover_file_id');
	}
	
	public function playlistWithBanner() {
		return $this->hasOne(self::$p.'Playlist', 'side_banner_file_id');
	}
}