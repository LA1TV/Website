<?php namespace uk\co\la1tv\website\models;

class OldFileId extends MyEloquent {

	protected $table = 'old_file_ids';
	protected $fillable = array('old_file_id');

	public function newFile() {
		return $this->belongsTo(self::$p.'File', 'new_file_id');
	}
}