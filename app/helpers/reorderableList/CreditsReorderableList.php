<?php namespace uk\co\la1tv\website\helpers\reorderableList;

use uk\co\la1tv\website\models\SiteUser;

abstract class CreditsReorderableList implements ReorderableList {
	
	private $valid = null;
	private $initialDataString = null;
	private $stringForInput = null;
	
	// $data should be the an array of {productionRoleId, siteUserId}, nameOverride}
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
			
			if (!isset($a['productionRoleId']) || !is_numeric($a['productionRoleId'])) {
				$this->valid = false;
				$currentItemOutput["productionRoleState"] = array(
					"id"	=> null,
					"text"	=> null
				);
			}
			else {
				// lookup the production role and replace the name
				$productionRoleId = intval($a['productionRoleId']);
				if ($productionRoleId === 0) {
					$productionRoleId = null;
				}
				$productionRole = null;
				if (!is_null($productionRoleId)) {
					$productionRole = $this->getProductionRoleModel()->find($productionRoleId);
				}
				
				$currentItemOutput['productionRoleState'] = array(
					"id"	=> null,
					"text"	=> null
				);
				if (!is_null($productionRole)) {
					$currentItemOutput['productionRoleState'] = array(
						"id"	=> intval($productionRole->id),
						"text"	=> $productionRole->getName()
					);
				}
				else {
					$this->valid = false;
				}
			}
			
			if (!isset($a['siteUserId'])) {
				$this->valid = false;
				$currentItemOutput["siteUserState"] = array(
					"id"	=> null,
					"text"	=> null
				);
			}
			else if (is_null($a['siteUserId']) || !is_numeric($a['siteUserId'])) {
				// a check will be made later to ensure that the nameOverride is provided
				$currentItemOutput["siteUserState"] = array(
					"id"	=> null,
					"text"	=> null
				);
			}
			else {
				// lookup the user and set name
				$siteUserId = intval($a['siteUserId']);
				if ($siteUserId === 0) {
					$siteUserId = null;
				}
				$siteUser = null;
				if (!is_null($siteUserId)) {
					$siteUser = SiteUser::find($siteUserId);
				}
				
				$currentItemOutput['siteUserState'] = array(
					"id"	=> null,
					"text"	=> null
				);
				if (!is_null($siteUser)) {
					$currentItemOutput['siteUserState'] = array(
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
				$currentItemOutput["siteUserState"] = null;
			}
			
			if (
				(!is_null($currentItemOutput['siteUserState']['id']) && $currentItemOutput["nameOverride"] !== "") ||
				(is_null($currentItemOutput['siteUserState']['id']) && $currentItemOutput["nameOverride"] === "")
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
	
	protected abstract function getProductionRoleModel();
	
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