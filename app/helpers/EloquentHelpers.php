<?php

class EloquentHelpers {
	
	// calls associate() on the relation with the model if the model is not null
	// if the model is null then the field in the model containing the foreign key will be set to null
	public static function associateOrNull($relation, $foreignModel) {
		if (!is_null($foreignModel)) {
			$relation->associate($foreignModel);
		}
		else {
			self::setForeignKeyNull($relation);
		}
	}
	
	public static function setForeignKeyNull($relation) {
		$model = $relation->getParent();
		$model[$relation->getForeignKey()] = null;
	}
}