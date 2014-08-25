<?php namespace uk\co\la1tv\website\helpers\reorderableList;

class AjaxSelectReorderableList implements ReorderableList {
	
	private $valid = null;
	private $stringForReordableList = null;
	private $stringForInput = null;
	
	// $data should be the an array of ids
	// does not need to be valid. Anything invalid will be ignored.
	// $queryBuilder should be a callback that returns a DB query which will then be used to get the models for the table
	// $getTextCallback should be a function that returns the text to be displayed on the row from the model passed into the first param.
	public function __construct($data, $queryBuilder, $getTextCallback) {
		$foundDuplicates = false;
		if (!is_array($data)) {
			$this->valid = false;
			$this->stringForReordableList = json_encode(array());
			$this->stringForInput = json_encode(array());
			return;
		}
		
		$this->valid = true;
		$output = array();
		$ids = array();
		foreach($data as $a) {
			if (is_int($a)) {
				if (!in_array($a, $ids, true)) {
					$ids[] = $a;
				}
				else {
					$this->valid = false;
				}
			}
			else {
				$this->valid = false;
			}
			$output[] = array(
				"id"	=> is_int($a) ? $a : null,
				"text"	=> null
			);
		}
		if (count($ids) > 0) {
			// the queryBuilder callback should select the correct table.
			$models = $queryBuilder()->whereIn("id", $ids)->get();
			$modelsIds = array();
			foreach($models as $a) {
				$modelIds[] = intval($a->id);
			}
			foreach($output as $i=>$a) {
				if (is_null($a['id'])) {
					continue;
				}
				$modelIndex = array_search($a['id'], $modelIds, true);
				if ($modelIndex === false) {
					$output[$i]["id"] = null; // if the model can't be found anymore make the id null as well.
					$this->valid = false;
					continue;
				}
				$output[$i]["text"] = $getTextCallback($models[$modelIndex]);
			}
		}
		$this->stringForReordableList = json_encode($output);
		$this->stringForInput = json_encode($modelIds);
	}
	
	// returns true if the $data is valid and all related models exist.
	public function isValid() {
		return $this->valid;
	}
	
	// if there is invalid data in $data this will be handled.
	public function getStringForReorderableList() {
		return $this->stringForReordableList;
	}
	
	// if there is invalid data in $data this will be handled.
	public function getStringForInput() {
		return $this->stringForInput;
	}
}