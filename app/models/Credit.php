<?php namespace uk\co\la1tv\website\models;
use Exception;

class Credit extends MyEloquent {

	protected $table = 'credits';
	protected $fillable = array('name_override');
	
	protected static function boot() {
		parent::boot();
		self::saving(function($model) {
			if (is_null($model->siteUser) === is_null($model->name_override)) {
				throw(new Exception("Either SiteUser or a name override must be provided but not both."));
			}
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