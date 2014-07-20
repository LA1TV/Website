<?php namespace uk\co\la1tv\website\models;

use Eloquent;
use DB;
use FormHelpers;

// custom extension of eloquent that wraps the 'save' and 'push' in transactions
class MyEloquent extends Eloquent {
	
	protected static $p = "uk\\co\\la1tv\\website\\models\\";
	
	public function push() {
		$returnVal = NULL;
		DB::transaction(function() use (&$returnVal) {
			$returnVal = parent::push();
		});
		return $returnVal;
	}
	
	public function save(array $options = array()) {
		$returnVal = NULL;
		DB::transaction(function() use (&$returnVal, &$options) {
			$returnVal = parent::save($options);
		});
		return $returnVal;
	}
	
	// $column can be a string or an array of strings to search multiple columns
	public function scopeWhereContains($q, $column, $value, $fromLeft=false, $fromRight=false) {
		$escapedVal = str_replace("%", "|%", $value);
		$leftTmp = !$fromLeft ? "%" : "";
		$rightTmp = !$fromRight ? "%" : "";
		
		$columns = !is_array($column) ? array($column) : $column;
		$q->where(function($q2) use (&$escapedVal, &$leftTmp, &$rightTmp, &$columns) {
			foreach($columns as $a) {
				$a = str_replace("`", "", $a);
				$q2 = $q2->orWhereRaw("`" . $a . "` LIKE ".DB::connection()->getPdo()->quote($leftTmp . $escapedVal . $rightTmp)." ESCAPE '|'");
			}
		});
		return $q;
	}
	
	public function scopeUsePagination($q) {
		return $q->skip(FormHelpers::getPageStartIndex())->take(FormHelpers::getPageNoItems());
	}
}