<?php namespace uk\co\la1tv\website\models;

class CoverArtFile extends MyEloquent {

	protected $table = 'cover_art_files';
	protected $fillable = array();
	
	public function sourceFile() {
		return $this->belongsTo(self::$p.'File', 'source_file_id');
	}
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
	
}