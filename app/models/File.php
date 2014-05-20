<?php namespace uk\co\la1tv\website\models;

use Eloquent;

class File extends Eloquent {

	protected $table = 'files';
	protected $fillable = array('in_use');
	
	public function mediaItemWithCover() {
		return $this->belongsTo('MediaItem', 'cover_file_id');
	}
	
	public function mediaItemWithBanner() {
		return $this->belongsTo('MediaItem', 'side_banner_file_id');
	}
	
	public function playlistWithCover() {
		return $this->belongsTo('Playist', 'cover_file_id');
	}
	
	public function playlistWithBanner() {
		return $this->belongsTo('MediaItem', 'side_banner_file_id');
	}
}