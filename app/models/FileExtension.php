<?php namespace uk\co\la1tv\website\models;

class FileExtension extends MyEloquent {

	protected $table = 'file_extensions';
	protected $fillable = array('id', 'extension');
	
	public function fileTypes() {
		return $this->belongsToMany(self::$p.'FileType', 'file_extension_to_file_type', 'file_extension_id', 'file_type_id');
	}

}