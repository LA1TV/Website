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

	public function scopeSearch($q, $value) {
		if ($value === "") {
			return $q;
		}
		
		// if name is overridden here, then search the name here,
		// otherwise search the name on the ProductionRole model
		return $q->where(function($q2) use (&$value) {
			$q2->where(function($q3) use (&$value) {
				$q3->whereNotNull("name_override")->whereContains(array("name_override"), $value);
			})->orWhere(function($q3) use (&$value) {
				$q3->whereNull("name_override")->whereHas("productionRole", function($q4) use (&$value) {
					$q4->whereContains(array("name"), $value);
				});
			});
		});
	}
}