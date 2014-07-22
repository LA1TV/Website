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
		
		foreach($columns as $b=>$a) {
			$columns[$b] = "`".str_replace("`", "", $a)."`";
		}
		
		$q->where(function($q2) use (&$escapedVal, &$leftTmp, &$rightTmp, &$columns) {
			foreach($columns as $a) {
				$q2 = $q2->orWhereRaw($a . " LIKE ".DB::connection()->getPdo()->quote($leftTmp . $escapedVal . $rightTmp)." ESCAPE '|'");
			}
		});
		return $q;
	}
	
	// http://stackoverflow.com/a/11144591/1048589
	// if $orEquals is true then any records with a column that matches the $val exactly will always be returned.
	// this is useful for cases where the actual value in the field is less than the minimum length that the match command works with.
	public function scopeWhereMatch($q, $column, $val, $orEquals=true) {
		$columns = !is_array($column) ? array($column) : $column;
		$escapedColumns = array();
		foreach($columns as $a) {
			$escapedColumns[] = "`".str_replace("`", "", $a)."`";
		}
	
		$q->whereRaw("MATCH (".implode($escapedColumns, ",").") AGAINST (".DB::connection()->getPdo()->quote($val)." IN NATURAL LANGUAGE MODE)");
		if ($orEquals) {
			foreach($columns as $a) {
				$q = $q->orWhere($a, $val);
			}
		}
		return $q;
	}
	
	public function scopeUsePagination($q) {
		return $q->skip(FormHelpers::getPageStartIndex())->take(FormHelpers::getPageNoItems());
	}
}