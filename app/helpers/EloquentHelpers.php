<?php

use Illuminate\Database\Eloquent\Model;

class EloquentHelpers {
	
	// calls associate() on the relation with the model if the model is not null
	// if the model is null then the field in the model containing the foreign key will be set to null
	public static function associateOrNull($model, $relationName, $foreignModel) {
		if (!is_null($foreignModel)) {
			$model->$relationName()->associate($foreignModel);
		}
		else {
			self::setForeignKeyNull($model, $relationName);
		}
	}
	
	public static function setForeignKeyNull($model, $relationName) {
		$model[$model->$relationName()->getForeignKey()] = null;
	}
}