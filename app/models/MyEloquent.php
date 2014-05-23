<?php namespace uk\co\la1tv\website\models;

use Eloquent;
use DB;

// custom extension of eloquent that wraps the 'save' and 'push' in transactions
class MyEloquent extends Eloquent {
	
	protected static $p = "uk\\co\\la1tv\\website\\models\\";
	
	// can't use $transaction because cause php error on server for some reason when trying to access parent:: from closure
	// Cannot access parent:: when no class scope is active
	
	public function push() {
		$returnVal = NULL;
		DB::beginTransaction();
		try {
			$returnVal = parent::push();
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
		return $returnVal;
	}
	
	public function save(array $options = array()) {
		$returnVal = NULL;
		DB::beginTransaction();
		try {
			$returnVal = parent::save($options);
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
		return $returnVal;
	}
}