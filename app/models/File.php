<?php namespace uk\co\la1tv\website\models;

class File extends MyEloquent {

	protected $table = 'files';
	protected $fillable = array('in_use', 'filename', 'size', 'session_id', 'ready_for_delete');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->exists && $model->original["ready_for_delete"]) {
				throw(new Exception("This file is pending deletion and can no longer be modified."));
			}
			return true;
		});
	}
	
	public function fileType() {
		return $this->belongsTo(self::$p.'FileType', 'file_type_id');
	}
	
	public function uploadPoint() {
		return $this->belongsTo(self::$p.'UploadPoint', 'upload_point_id');
	}
	
	public function mediaItemWithFile() {
		return $this->hasOne(self::$p.'MediaItem', 'source_file_id');
	}
	
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
	
	public function videoFiles() {
		return $this->hasMany(self::$p.'VideoFile', 'source_file_id');
	}
	
	public function getExtension() {
		return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
	}
	
	public function markReadyForDelete() {
		$this->in_use = false;
		$this->ready_for_delete = true;
	}

}