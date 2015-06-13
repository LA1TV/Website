<?php namespace uk\co\la1tv\website\models;

class ProductionRole extends MyEloquent {

	protected $table = 'production_roles';
	protected $fillable = array('name', 'description');
	
	public function credits() {
		$this->hasMany(self::$p.'Credit', 'production_role_id');
	}
	
	public function productionRolePlaylist() {
		$this->hasOne(self::$p.'ProductionRolePlaylist', 'production_role_id');
	}
	
	public function productionRoleMediaItem() {
		$this->hasOne(self::$p.'ProductionRoleMediaItem', 'production_role_id');
	}

}