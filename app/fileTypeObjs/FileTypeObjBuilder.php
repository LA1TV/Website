<?php namespace uk\co\la1tv\website\fileTypeObjs;

use uk\co\la1tv\website\models\FileType;
use Exception;

class FileTypeObjBuilder {
	
	public static function build(FileType $fileType) {
		$id = intval($fileType->id, 10);
		$fileType = null;
		
		if ($id === 1) { // side banner images
			$fileType = new SideBannerImage();
		}
		else if ($id === 2) { // cover images
			$fileType = new CoverImage();
		}
		else if ($id === 3) { // video uploads
			$fileType = new VideoUpload();
		}
		else if ($id === 4) { // cover art for media
			$fileType = new CoverArt();
		}
		else {
			throw(new Exception("Invalid file type id."));
		}
		return $fileType;
	}
}