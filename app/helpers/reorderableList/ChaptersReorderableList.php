<?php namespace uk\co\la1tv\website\helpers\reorderableList;

class ChaptersReorderableList implements ReorderableList {
	
	private $valid = null;
	private $initialDataString = null;
	private $stringForInput = null;
	
	// $data should be the an array of {title, time}
	// will be handled if this is not the format of the data, and obviously flagged as invalid
	// does not need to be valid. Anything invalid will be ignored.
	public function __construct($data) {
		$foundDuplicates = false;
		if (!is_array($data)) {
			$this->valid = false;
			$this->initialDataString = json_encode(array());
			$this->stringForInput = json_encode(array());
			return;
		}
		
		$this->valid = true;
		$output = array();
		$times = array();
		foreach($data as $a) {
			if (!isset($a['title']) || !isset($a['time'])) {
				$this->valid = false;
				$output[] = array(
					"title"	=> "",
					"time"	=> null
				);
				continue;
			}
			
			$timeInvalid = false;
			if (trim($a['title']) === "") {
				$this->valid = false;
			}
			else if (!is_int($a['time']) || $a['time'] < 0) {
				$this->valid = false;
				$timeInvalid = true;
			}
			else {
				if (in_array($a['time'], $times)) {
					$this->valid = false;
				}
				else {
					$times[] = $a['time'];
				}
			}
			
			$output[] = array(
				"title"	=> $a['title'],
				"time"	=> !$timeInvalid ? $a['time'] : null
			);
		}
	
		// the string for the input and the initial data string are the same for the chapters reordable list
		$this->initialDataString = json_encode($output);
		$this->stringForInput = json_encode($output);
	}
	
	// returns true if the $data is valid and all related models exist.
	public function isValid() {
		return $this->valid;
	}
	
	// if there is invalid data in $data this will be handled.
	public function getInitialDataString() {
		return $this->initialDataString;
	}
	
	// if there is invalid data in $data this will be handled.
	public function getStringForInput() {
		return $this->stringForInput;
	}
}