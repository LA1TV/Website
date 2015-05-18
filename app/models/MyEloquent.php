<?php namespace uk\co\la1tv\website\models;

use Eloquent;
use DB;
use FormHelpers;

class MyEloquent extends Eloquent {
	
	protected static $p = "uk\\co\\la1tv\\website\\models\\";
	
	// $column can be a string or an array of strings to search multiple columns
	// if you want to search on a column in a relation then pass in an array as the column with the first element being the relation name, and the second element being the column
	public function scopeWhereContains($q, $column, $value, $fromLeft=false, $fromRight=false) {
		$escapedVal = str_replace("%", "|%", $value);
		$escapedVal = str_replace("_", "|_", $escapedVal);
		$leftTmp = !$fromLeft ? "%" : "";
		$rightTmp = !$fromRight ? "%" : "";
		
		$columns = !is_array($column) ? array($column) : $column;
		
		$currentTableColumns = array(); // array of columns contained on this table
		$relationTableColumns = array(); // array where key is relation and value is array if columns to be checked on that relation
		foreach($columns as $a) {
			if (!is_array($a)) {
				$currentTableColumns[] = $a;
			}
			else {
				if (!isset($relationTableColumns[$a[0]])) {
					$relationTableColumns[$a[0]] = array($a[1]);
				}
				else {
					$relationTableColumns[$a[0]][] = $a[1];
				}
			}
		}
		
		$q->where(function($q2) use (&$escapedVal, &$leftTmp, &$rightTmp, &$currentTableColumns) {
			foreach($currentTableColumns as $a) {
				$q2 = $q2->orWhereRaw("`".str_replace("`", "", $a)."`" . " LIKE ".DB::connection()->getPdo()->quote($leftTmp . $escapedVal . $rightTmp)." ESCAPE '|'");
			}
		});
		
		// this could be more efficient if it was with a join instead of using laravels relation system, but laravel's relation system makes it easier, and is normally just as efficient.
		// This method results in an inner query, even if the relation has already been loaded in laravel with 'with', but it's still only one inner query per relation, not column, so it's ok.
		foreach($relationTableColumns as $relation=>$cols) {
			$q->orWhereHas($relation, function($q2) use (&$escapedVal, &$leftTmp, &$rightTmp, &$cols) {
				// this extra where is required because laravel inserts a where caluse for the nested query's foreign key to match the outer one
				// it doesn't automatically put the rest in brackets so would end up as inner.id = outer.id OR something. This makes sure it's inner.id = outer.id AND (something)
				$q2->where(function($q3) use (&$escapedVal, &$leftTmp, &$rightTmp, &$cols) {
					foreach($cols as $a) {
						$q3 = $q3->orWhereRaw("`".str_replace("`", "", $a)."`" . " LIKE ".DB::connection()->getPdo()->quote($leftTmp . $escapedVal . $rightTmp)." ESCAPE '|'");
					}
				});
			});
		}
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