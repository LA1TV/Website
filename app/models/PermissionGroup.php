<?php namespace uk\co\la1tv\website\models;

use uk\co\la1tv\website\helpers\reorderableList\AjaxSelectReorderableList;

class PermissionGroup extends MyEloquent {

	protected $table = 'permission_groups';
	protected $fillable = array('name', 'description', 'position');

	public function users() {
		return $this->belongsToMany(self::$p.'User', 'user_to_group', 'group_id', 'user_id');
	}
	
	public function permissions() {
		return $this->belongsToMany(self::$p.'Permission', 'permission_to_group', 'group_id', 'permission_id')->withPivot('permission_flag');
	}
	
	public function getNameAndDescription() {
		$text = $this->name;
		if (!is_null($this->description)) {
			$text .= " (".$this->description.")";
		}
		return $text;
	}
	
	public function scopeSearch($q, $value) {
		return $value === "" ? $q : $q->whereContains(array("name", "description"), $value);
	}
	
	public static function isValidIdsFromAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new PermissionGroup();
		}, function($model) {
			return $model->getNameAndDescription();
		});
		return $reorderableList->isValid();
	}
	
	public static function generateInitialDataForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new PermissionGroup();
		}, function($model) {
			return $model->getNameAndDescription();
		});
		return $reorderableList->getInitialDataString();
	}
	
	public static function generateInputValueForAjaxSelectOrderableList($ids) {
		$reorderableList = new AjaxSelectReorderableList($ids, function() {
			return new PermissionGroup();
		}, function($model) {
			return $model->getNameAndDescription();
		});
		return $reorderableList->getStringForInput();
	}
}