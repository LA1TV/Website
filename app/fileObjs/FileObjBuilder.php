<?php namespace uk\co\la1tv\website\fileObjs;

use uk\co\la1tv\website\models\File;
use Exception;

// There should be a FileObj object associated with each file.
// Therefore we cache an object when it's build with the file id so that that instance can be retrieved in the future if it is 'built' again

class FileObjBuilder {

	// TODO: add listener to File models and remove item from cache if file model is deleted

	// array of FileObjs with the key being the file id.
	private static $cache = array();
	
	
	// build the object if an instance has not already been created for the file, otherwise return the cached one
	public static function retrieve(File $file) {
		
		// check if object already built and in cache and return it if this is the case
		if (isset(self::$cache[$file->id])) {
			return self::$cache[$file->id];
		}
	
		$typeId = intval($file->{$file->fileType()->getForeignKey()}, 10);
		$fileObj = null;
		
		if ($typeId === 1) { // side banner images
			$fileObj = new SideBannerImage($file);
		}
		else if ($typeId === 2) { // cover images
			$fileObj = new CoverImage($file);
		}
		else if ($typeId === 3) { // video uploads
			$fileObj = new VideoUpload($file);
		}
		else if ($typeId === 4) { // cover art for media
			$fileObj = new CoverArt($file);
		}
		else {
			throw(new Exception("Invalid file type id."));
		}
		
		// add to cache
		self::$cache[$file->id] = $fileObj;
		
		return $fileObj;
	}
}