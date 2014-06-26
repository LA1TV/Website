<?php namespace uk\co\la1tv\website\models;

use EloquentHelpers;
use Exception;
use uk\co\la1tv\website\fileTypeObjs\FileTypeObjBuilder;

// File models should be created and managed using the Upload service provider.

class File extends MyEloquent {

	protected $table = 'files';
	protected $fillable = array('in_use', 'filename', 'size', 'session_id');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if ($model->exists && $model->original["in_use"] && !$model->in_use && !$model->ready_for_delete) {
				throw(new Exception("The file can only be marked in_use once."));
			}
			else if ($model->exists && $model->original["ready_for_delete"]) {
				throw(new Exception("This file is pending deletion and can no longer be modified."));
			}
			else if (!$model->in_use && (
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithFile()) ||
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithCover()) ||
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithBanner()) ||
				!EloquentHelpers::getIsForeignNull($model->playlistWithCover()) ||
				!EloquentHelpers::getIsForeignNull($model->playlistWithBanner()) ||
				!EloquentHelpers::getIsForeignNull($model->vodVideoGroups()) ||
				!EloquentHelpers::getIsForeignNull($model->videoFile()) ||
				!EloquentHelpers::getIsForeignNull($model->thumbnailFiles()) ||
				!EloquentHelpers::getIsForeignNull($model->thumbnailFile())
				)) {
				throw(new Exception("File must be marked as in use before it can belong to anything."));
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
	
	public function vodVideoGroups() {
		return $this->hasMany(self::$p.'VodVideoGroup', 'source_file_id');
	}
	
	public function videoFile() {
		return $this->hasOne(self::$p.'VideoFile', 'file_id');
	}
	
	public function thumbnailFiles() {
		return $this->hasMany(self::$p.'CoverArtFile', 'source_file_id');
	}
	
	public function thumbnailFile() {
		return $this->hasOne(self::$p.'CoverArtFile', 'file_id');
	}
	
	public function getExtension() {
		return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
	}
	
	// this should not be called directly. This should be managed from the Upload service provider
	public function markReadyForDelete() {
		$this->in_use = false;
		$this->ready_for_delete = true;
	}
	
	public function getFileTypeObj() {
		return FileTypeObjectBuilder::retrieve($this);
	}

}