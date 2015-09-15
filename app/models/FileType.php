<?php namespace uk\co\la1tv\website\models;

class FileType extends MyEloquent {

	protected $table = 'file_types';
	protected $fillable = array('id', 'description', 'mime_type');
	
	public function extensions() {
		return $this->belongsToMany(self::$p.'FileExtension', 'file_extension_to_file_type', 'file_type_id', 'file_extension_id');
	}
	
	public function getExtensionsArray() {
		$extensions = array();
		foreach($this->extensions as $a) {
			$extensions[] = $a->extension;
		}
		return $extensions;
	}
	
	public function files() {
		return $this->hasMany(self::$p.'File', 'file_type_id');
	}
	
	public function uploadPoints() {
		return $this->hasMany(self::$p.'UploadPoint', 'file_type_id');
	}
}