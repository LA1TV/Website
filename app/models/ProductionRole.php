<?php namespace uk\co\la1tv\website\models;

class ProductionRole extends MyEloquent {

	protected $table = 'production_roles';
	protected $fillable = array('name', 'description');
	
	public function credits() {
		$this->hasMany(self::$p.'Credit', 'production_role_id');
	}

}