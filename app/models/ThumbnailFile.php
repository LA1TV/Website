<?php namespace uk\co\la1tv\website\models;

class ThumbnailFile extends MyEloquent {

	protected $table = 'thumbnail_files';
	protected $fillable = array(); // TODO: check this does as intended when empty
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
	
}