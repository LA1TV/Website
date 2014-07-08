<?php namespace uk\co\la1tv\website\models;

use EloquentHelpers;
use Exception;

// FILE MODELS SHOULD NOT BE CREATED MANUALLY. They should be created and managed using the Upload service provider.

class File extends MyEloquent {

	protected $table = 'files';
	protected $fillable = array('in_use', 'filename', 'size', 'session_id');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			
			$parentFileForeignKey = $model->parentFile()->getForeignKey();
			$uploadPointForeignKey = $model->uploadPoint()->getForeignKey();
			
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
				!EloquentHelpers::getIsForeignNull($model->playlistWithBanner())
				)) {
				throw(new Exception("File must be marked as in use before it can belong to anything."));
			}
			else if (	($model->exists && $model->original[$parentFileForeignKey] !== $model->$parentFileForeignKey) ||
						(!$model->exists && !is_null($model->$parentFileForeignKey))
					) {
				throw(new Exception("The parent file should only be set externally."));
			}
			else if ($model->exists && $model->original[$uploadPointForeignKey] !== $model->$uploadPointForeignKey) {
				throw(new Exception("The upload point can only be set on creation."));
			}
			return true;
		});
	}
	
	public function getProcessStateAttribute($state) {
		$stateInt = intval($state, 10);
		if ($stateInt < 0 || $stateInt > 2) {
			throw(new Exception("Invalid process state."));
		}
		return $state;
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
	
	public function videoFile() {
		return $this->hasOne(self::$p.'VideoFile', 'file_id');
	}

	public function imageFile() {
		return $this->hasOne(self::$p.'ImageFile', 'file_id');
	}
	
	public function coverArtFile() {
		return $this->hasOne(self::$p.'MediaItemVideo', 'cover_art_file_id');
	}
	
	public function parentFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function sourceFiles() {
		return $this->hasMany(self::$p.'File', 'source_file_id');
	}
	
	public function getExtension() {
		return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
	}
	
	// THIS SHOULD NOT BE CALLED DIRECTLY. This should be managed from the Upload service provider
	public function markReadyForDelete() {
		$this->in_use = false;
		$this->ready_for_delete = true;
	}
	
	//returns array containing these keys;
	//  - error (boolean)
	//  - processStage (int)
	//  - processPercentage (int [0-100], may be null)
	//  - msg (may be null)
	public function getProcessInfo() {
		return array(
			"state"			=>  intval($this->process_state, 10),
			"percentage"	=> !is_null($this->process_percentage) ? intval($this->process_percentage, 10) : null,
			"msg"			=> $this->msg
		);
	}

}