<?php namespace uk\co\la1tv\website\fileObjs;

use uk\co\la1tv\website\models\File;
use Exception;

// There should be a FileObj object associated with each file.
// Therefore we cache an object when it's build with the file id so that that instance can be retrieved in the future if it is 'built' again

// TODO: if a file model is deleted and is in the cache it should be removed from the cache.
// doesn't appear to be a neat way of doing this at the moment due to the way that the eloquent callbacks work and the fact that php doesn't support inner classes

class FileObjBuilder {
	
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
		else if ($typeId === 3) { // video source uploads
			$fileObj = new VideoUpload($file);
		}
		else if ($typeId === 4) { // cover art for media
			$fileObj = new CoverArt($file);
		}
		else if ($typeId === 5) { //vod video renders
			$fileObj = new VideoRender($file);
		}
		else {
			throw(new Exception("Invalid file type id."));
		}
		
		// add to cache
		self::$cache[$file->id] = $fileObj;
		
		return $fileObj;
	}
}