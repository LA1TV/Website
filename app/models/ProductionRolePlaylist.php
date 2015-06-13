<?php namespace uk\co\la1tv\website\models;

class ProductionRolePlaylist extends MyEloquent {

	protected $table = 'production_roles_playlist';
	protected $fillable = array('name', 'description');
	
	public function productionRole() {
		$this->belongsTo(self::$p.'ProductionRole', 'production_role_id');
	}

}