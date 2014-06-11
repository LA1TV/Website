<?php namespace uk\co\la1tv\website\models;

class FileType extends MyEloquent {

	protected $table = 'file_types';
	protected $fillable = array('description');
	
	public function fileExtensions() {
		return $this->belongsToMany(self::$p.'FileExtension', 'file_extension_to_file_type', 'file_type_id', 'file_extension_id');
	}
	
	public function files() {
		return $this->hasMany(self::$p.'File', 'file_type_id');
	}
}