<?php namespace uk\co\la1tv\website\models;

use EloquentHelpers;
use Exception;
use Session;
use Config;

// FILE MODELS SHOULD NOT BE CREATED MANUALLY. They should be created and managed using the Upload service provider.

class File extends MyEloquent {

	protected $table = 'files';
	protected $fillable = array('in_use', 'filename', 'size', 'session_id');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			
			$sourceFileForeignKey = $model->sourceFile()->getForeignKey();
			$uploadPointForeignKey = $model->uploadPoint()->getForeignKey();
			
			if ($model->exists && $model->original["in_use"] && !$model->in_use && !$model->ready_for_delete) {
				throw(new Exception("The file can only be marked in_use once."));
			}
			else if ($model->exists && $model->original["ready_for_delete"]) {
				throw(new Exception("This file is pending deletion and can no longer be modified."));
			}
			else if (!$model->in_use && (
				!EloquentHelpers::getIsForeignNull($model->mediaItemVideoWithFile()) ||
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithCover()) ||
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithBanner()) ||
				!EloquentHelpers::getIsForeignNull($model->mediaItemWithCoverArt()) ||
				!EloquentHelpers::getIsForeignNull($model->playlistWithCover()) ||
				!EloquentHelpers::getIsForeignNull($model->playlistWithBanner()) ||
				!EloquentHelpers::getIsForeignNull($model->playlistWithCoverArt())
				)) {
				throw(new Exception("File must be marked as in use before it can belong to anything."));
			}
			else if (	($model->exists && $model->original[$sourceFileForeignKey] !== $model->$sourceFileForeignKey) ||
						(!$model->exists && !is_null($model->$sourceFileForeignKey))
					) {
				throw(new Exception("The source file should only be set externally."));
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
	
	public function mediaItemVideoWithFile() {
		return $this->hasOne(self::$p.'MediaItemVideo', 'source_file_id');
	}
	
	public function mediaItemWithCoverArt() {
		return $this->hasOne(self::$p.'MediaItem', 'cover_art_file_id');
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
	
	public function playlistWithCoverArt() {
		return $this->hasOne(self::$p.'Playlist', 'cover_art_file_id');
	}
	
	public function videoFile() {
		return $this->hasOne(self::$p.'VideoFile', 'file_id');
	}

	public function imageFile() {
		return $this->hasOne(self::$p.'ImageFile', 'file_id');
	}
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function renderFiles() {
		return $this->hasMany(self::$p.'File', 'source_file_id');
	}
	
	// gets the File model which represents this source image file at that resolution
	// if that resolution is not available, or the file should not be availabel yet null is returned.
	// if this file model does not represent an image then an exception is thrown
	public function getImageFileWithResolution($w, $h) {
		if (!$this->getShouldBeAccessible()) {
			return null;
		}
		
		$imageRenders = $this->renderFiles;
		if (count($imageRenders) === 0) {
			// presuming that there must always be at least one image generated from the source image
			throw(new Exception("The current file is not an image."));
		}
		foreach($imageRenders as $a) {
			$imageFile = $a->imageFile;
			if (is_null($imageFile)) {
				// if this is an image render it will always have an entry in the image_files table.
				throw(new Exception("The current file is not an image."));
			}
			if (intval($imageFile->width) === $w && intval($imageFile->height) === $h) {
				return $a;
			}
		}
		return null;
	}
	
	// returns an array of form array("uri", "width", "height", "qualityDefinition")
	// returns null if processing hasn't finished yet or the file should not be available for some reason
	// returned in predefined qualities order
	public function getVideoFiles() {
		
		if (!$this->getShouldBeAccessible()) {
			return null;
		}
	
		$renders = $this->renderFiles;
		$videoFiles = array();
		$positions = array();
		if (count($renders) === 0) {
			// presuming that there must always be at least one video render from the source
			throw(new Exception("The current file is not a video."));
		}
		foreach($renders as $a) {
			$positions[] = intval($a->videoFile->qualityDefinition->position);
			$videoFiles[] = array(
				"uri"					=> $a->getUri(),
				"width"					=> $a->videoFile->width,
				"height"				=> $a->videoFile->height,
				"qualityDefinition"		=> $a->videoFile->qualityDefinition
			);
		}
		// reorder so in qualities order
		array_multisort($positions, SORT_NUMERIC, SORT_ASC, $videoFiles);
		return $videoFiles;
	}
	
	public function getExtension() {
		return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
	}
	
	public function isTemporaryFromCurrentSession() {
		return !$this->in_use && $this->session_id === Session::getId();
	}
	
	public function getFinishedProcessing() {
		return intval($this->process_state) === 1;
	}
	
	// returns true if this file is ready for public 
	public function getShouldBeAccessible() {
		return $this->in_use && $this->getFinishedProcessing();
	}
	
	public function scopeFinishedProcessing($q) {
		return $q->where("process_state", 1);
	}
	
	// returns a uri to the file if the file has finished processing and is in_use
	// returns null otherwise
	public function getUri() {
		if (!$this->getShouldBeAccessible()) {
			return null;
		}
		return Config::get("custom.base_url") . "/" . Config::get("uploads.retrieve_base_uri") . "/" . $this->id;
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