<?php namespace uk\co\la1tv\website\models;

class UploadPoint extends MyEloquent {

	protected $table = 'upload_points';
	protected $fillable = array('id', 'description');
	
	public function fileType() {
		return $this->belongsTo(self::$p.'FileType', 'file_type_id');
	}

}