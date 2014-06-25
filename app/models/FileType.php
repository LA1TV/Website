<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\fileTypeObjs\FileTypeObjBuilder;

class FileType extends MyEloquent {

	protected $table = 'file_types';
	protected $fillable = array('id', 'description');
	
	private static $fileTypeObjsCache = array();
	
	public function extensions() {
		return $this->belongsToMany(self::$p.'FileExtension', 'file_extension_to_file_type', 'file_type_id', 'file_extension_id');
	}
	
	public function files() {
		return $this->hasMany(self::$p.'File', 'file_type_id');
	}
	
	public function uploadPoints() {
		return $this->hasMany(self::$p.'UploadPoint', 'file_type_id');
	}
	
	public function getFileTypeObj() {
		if (isset(self::$fileTypeObjsCache[$this->id])) {
			return self::$fileTypeObjsCache[$this->id];
		}
		$obj = FileTypeObjBuilder::build($this);
		self::$fileTypeObjsCache[$this->id] = $obj;
		return $obj;
	}
}