<?php namespace uk\co\la1tv\website\models;

use EloquentHelpers;
use Exception;

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
			
			if ($model->exists && $model->original[$model->fileType()->getForeignKey()] !== $model->attributes[$model->fileType()->getForeignKey()]) {
				throw(new Exception("The file type cannot be changed."));
			}
			
			if (!$model->exists) {
				// TODO: the following line is repeated a lot because we only want the relation to be loaded if necessary. Can probably be tidied up
				$fileTypeObj = $model->fileType->getFileTypeObj();
				if (!$fileTypeObj->preCreation($model)) {
					throw(new Exception("Creation was cancelled by preCreation callback."));
				}
			}
			
			if ($model->in_use && !$model->original["in_use"]) {
				$fileTypeObj = $model->fileType->getFileTypeObj();
				if (!$fileTypeObj->preRegistration($model)) {
					throw(new Exception("Registration was cancelled by preRegistration callback."));
				}
			}
			
			if ($model->ready_for_delete && !$model->original["ready_for_delete"]) {
				$fileTypeObj = $model->fileType->getFileTypeObj();
				if (!$fileTypeObj->preDeletion($model)) {
					throw(new Exception("Deletion was cancelled by preDeletion callback."));
				}
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
	
	public function markReadyForDelete() {
		$this->in_use = false;
		$this->ready_for_delete = true;
	}

}