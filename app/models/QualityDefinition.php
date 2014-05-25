<?php namespace uk\co\la1tv\website\models;

class QualityDefinition extends MyEloquent {

	protected $table = 'quality_definitions';
	protected $fillable = array('id', 'name');
	
}