<?php namespace uk\co\la1tv\website\models;


class VodData extends MyEloquent {

	protected $table = 'vod_data';
	protected $fillable = array('duration');
	
	public function file() {
		return $this->belongsTo(self::$p.'File', 'file_id');
	}
}