<?php namespace uk\co\la1tv\website\models;

class Credit extends MyEloquent {

	protected $table = 'credits';
	protected $fillable = array('name_override');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			
			// TODO check doesn't have site user and name_override
			return true;
		});
	}
	
	public function creditable() {
		return $this->morphTo();
	}
	
	public function productionRole() {
		return $this->belongsTo(self::$p.'ProductionRole', 'production_role_id');
	}
	
	public function siteUser() {
		return $this->belongsTo(self::$p.'SiteUser', 'site_user_id');
	}

}