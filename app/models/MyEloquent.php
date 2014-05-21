<?php namespace uk\co\la1tv\website\models;

use Eloquent;
use DB;

// custom extension of eloquent that wraps the 'save' and 'push' in transactions
class MyEloquent extends Eloquent {
	
	public function push() {
		$returnVal = NULL;
		DB::transaction(function() use (&$returnVal) {
			$returnVal = $this->push();
		});
		return $returnVal;
	}
	
	public function save(array $options = array()) {
		$returnVal = NULL;
		DB::transaction(function() use (&$options, &$returnVal) {
			$returnVal = $this->save($options);
		});
		return $returnVal;
	}
}