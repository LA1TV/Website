<?php namespace uk\co\la1tv\website\helpers\reorderableList;

use uk\co\la1tv\website\models\SiteUser;

abstract class CreditsReorderableList implements ReorderableList {
	
	private $valid = null;
	private $initialDataString = null;
	private $stringForInput = null;
	
	// $data should be the an array of {productionRole:{id, text}, siteUser:{id, text}, nameOverride}
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
			
			if (!isset($a['productionRole'] || !isset($a['productionRole']['id'])) {
				$this->valid = false;
				$currentItemOutput["productionRole"] = array(
					"id"	=> null,
					"text"	=> ""
				);
			}
			else {
				// lookup the production role and replace the name
				$productionRoleId = intval($a['productionRole']['id']);
				// TODO check 0 is returned on error
				if ($productionRoleId === 0) {
					$productionRoleId = null;
				}
				$productionRole = null;
				if (!is_null($productionRoleId)) {
					$productionRole = $this->getProductionRoleModel()->find($productionRoleId);
				}
				
				$currentItemOutput['prouctionRole'] = array(
					"id"	=> null,
					"text"	=> ""
				);
				if (!is_null($productionRole)) {
					$currentItemOutput['prouctionRole'] = array(
						"id"	=> intval($productionRole->id),
						"text"	=> $productionRole->getName()
					);
				}
				else {
					$this->valid = false;
				}
			}
			
			if (!isset($a['siteUser'] || !isset($a['siteUser']['id'])) {
				$this->valid = false;
				$currentItemOutput["siteUser"] = array(
					"id"	=> null,
					"text"	=> ""
				);
			}
			else if (is_null($a['siteUser']['id'])) {
				// a check will be made later to ensure that the nameOverride is provided
				$currentItemOutput["siteUser"] = array(
					"id"	=> null,
					"text"	=> ""
				);
			}
			else {
				// lookup the user and replace the name
				$siteUserId = intval($a['siteUser']['id']);
				// TODO check 0 is returned on error
				if ($siteUserId === 0) {
					$siteUserId = null;
				}
				$siteUser = null;
				if (!is_null($siteUserId)) {
					$siteUser = SiteUser::find($siteUserId);
				}
				
				$currentItemOutput['siteUser'] = array(
					"id"	=> null,
					"text"	=> ""
				);
				if (!is_null($siteUser)) {
					$currentItemOutput['siteUser'] = array(
						"id"	=> intval($siteUser->id),
						"text"	=> $siteUser->name
					);
				}
				else {
					$this->valid = false;
				}
			}
			
			
			if (!isset($a['nameOverride']) || !is_string($a['nameOverride'])) {
				$this->valid = false;
				$currentItemOutput["nameOverride"] = "";
			}
			else {
				$a['nameOverride'] = trim($a['nameOverride']);
				$currentItemOutput["nameOverride"] = $a['nameOverride'];
			}
			
			if (
				(!is_null($currentItemOutput['siteUser']['id']) && $currentItemOutput["nameOverride"] !== "") ||
				(is_null($currentItemOutput['siteUser']['id']) && $currentItemOutput["nameOverride"] === "")
			) {
				// either a site user must be provided or a name
				$this->valid = false;
			}
			$output[] = $currentItemOutput;
		}
	
		// the string for the input and the initial data string are the same for the chapters reordable list
		$this->initialDataString = json_encode($output);
		$this->stringForInput = json_encode($output);
	}
	
	private abstract function getProductionRoleModel();
	
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