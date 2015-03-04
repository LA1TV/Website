<?php namespace uk\co\la1tv\website\models;

class CustomUri extends MyEloquent {

	protected $table = 'custom_uris';
	protected $fillable = array('id', 'name');
	
	public function uriable() {
		return $this->morphTo();
	}

}