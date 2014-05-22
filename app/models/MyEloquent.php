<?php namespace uk\co\la1tv\website\models;

use Eloquent;
use DB;

// custom extension of eloquent that wraps the 'save' and 'push' in transactions
class MyEloquent extends Eloquent {
	
	protected static $p = "uk\\co\\la1tv\\website\\models\\";
	
	public function push() {
		$returnVal = NULL;
		$fn = function() {
			parent::push();
		};
		DB::transaction(function() use (&$returnVal, &$fn) {
			$returnVal = $fn();
		});
		return $returnVal;
	}
	
	public function save(array $options = array()) {
		$returnVal = NULL;
		$fn = function($options) {
			parent::save($options);
		};
		DB::transaction(function() use (&$options, &$returnVal, &$fn) {
			$returnVal = $fn($options);
		});
		return $returnVal;
	}
}