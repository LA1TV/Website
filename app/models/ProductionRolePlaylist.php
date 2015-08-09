<?php namespace uk\co\la1tv\website\models;

class ProductionRolePlaylist extends MyEloquent {

	protected $table = 'production_roles_playlist';
	protected $fillable = array('name_override', 'description_override');
	
	public function productionRole() {
		return $this->belongsTo(self::$p.'ProductionRole', 'production_role_id');
	}
	
	public function getName() {
		return !is_null($this->name_override) ? $this->name_override : $this->productionRole->name;
	}
	
	public function getDescription() {
		return !is_null($this->description_override) ? $this->description_override : $this->productionRole->description;
	}
}