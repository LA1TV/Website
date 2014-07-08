<?php namespace uk\co\la1tv\website\models;

class ImageFile extends MyEloquent {

	protected $table = 'image_files';
	protected $fillable = array('width', 'height');
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
	
}