<?php namespace uk\co\la1tv\website\helpers\reorderableList;

use uk\co\la1tv\website\models\QualityDefinition;

class StreamUrlsReorderableList implements ReorderableList {
	
	private $valid = null;
	private $initialDataString = null;
	private $stringForInput = null;
	
	// $data should be the an array of {qualityDefinition: {id, text}, url, dvr, support, type}
	// will be handled if this is not the format of the data, and obviously flagged as invalid
	// does not need to be valid. Anything invalid will be ignored.
	public function __construct($data) {
		if (!is_array($data)) {
			$this->valid = false;
			$this->initialDataString = json_encode(array());
			$this->stringForInput = json_encode(array());
			return;
		}
		
		$this->valid = true;
		$output = array();
		foreach($data as $a) {
			
			$currentItemOutput = array();
			
			if (!isset($a['qualityState']) || !isset($a['qualityState']['id']) || !is_numeric($a['qualityState']['id'])) {
				$this->valid = false;
				$currentItemOutput["qualityState"] = array(
					"id"	=> null,
					"text"	=> ""
				);
			}
			else {
				// lookup the quality definition and replace the name
				$qualityDefinitionId = intval($a['qualityState']['id']);
				if ($qualityDefinitionId === 0) {
					$qualityDefinitionId = null;
				}
				$qualityDefinition = null;
				if (!is_null($qualityDefinitionId)) {
					$qualityDefinition = QualityDefinition::find($qualityDefinitionId);
				}
				
				$currentItemOutput['qualityState'] = array(
					"id"	=> null,
					"text"	=> ""
				);
				if (!is_null($qualityDefinition)) {
					$currentItemOutput['qualityState'] = array(
						"id"	=> intval($qualityDefinition->id),
						"text"	=> $qualityDefinition->name
					);
				}
				else {
					$this->valid = false;
				}
			}
			
			if (!isset($a['url']) || !is_string($a['url'])) {
				$this->valid = false;
				$currentItemOutput["url"] = "";
			}
			else {
				$a['url'] = trim($a['url']);
				if (filter_var($a['url'], FILTER_VALIDATE_URL) === false) {
					$this->valid = false;
				}
				$currentItemOutput["url"] = $a['url'];
			}
			
			if (!isset($a["type"]) || !is_string($a["type"])) {
				$this->valid = false;
				$currentItemOutput["type"] = "";
			}
			else {
				$a['type'] = trim($a['type']);
				if ($a['type'] === "" || preg_match('/\s/', $a['type'])) {
					// empty or contains whitespace
					// this could check a lot more but not really fussed
					$this->valid = false;
				}
				$currentItemOutput["type"] = $a['type'];
			}
			
			if (!isset($a['dvr']) && !is_bool($a['dvr'])) {
				$this->valid = false;
				$currentItemOutput["dvr"] = false;
			}
			else {
				$currentItemOutput["dvr"] = $a['dvr'];
			}
			
			// type must be this if using dvr bridge service as this is the type the dvr bridge service uses
			if ($currentItemOutput["dvr"] && $currentItemOutput["type"] !== "application/x-mpegURL") {
				$this->valid = false;
			}
			
			$currentItemOutput["dvr"] = isset($a['dvr']) && $a['dvr'] === true;
			
			if (!isset($a["support"]) || ($a["support"] !== "all" && $a["support"] !== "pc" && $a["support"] !== "mobile" && $a["support"] !== "none")) {
				$this->valid = false;
				$currentItemOutput["support"] = "all";
			}
			else {
				$currentItemOutput["support"] = $a['support'];
			}
			
			$output[] = $currentItemOutput;
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