<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class Playlist extends Eloquent {

	protected $table = 'playlists';
	protected $fillable = array('name', 'enabled', 'scheduled_publish_time', 'description', 'is_series');
	
	public function coverFile() {
		return $this->hasOne('File', 'cover_file_id');
	}
	
	public function sideBannerFile() {
		return $this->hasOne('File', 'side_banner_file_id');
	}

	
}