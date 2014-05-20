<?php namespace uk\co\la1tv\website\models;

class LiveStreamQuality {

	private $qualityId;
	private $description;
	private $position;
	private $created;
	private $updated;
	
	public function Stream($qualityId, $description, $position, $created, $updated) {
		$this->qualityId = $qualityId;
		$this->description = $description;
		$this->position = $position;
		$this->created = $created;
		$this->updated = $updated;
	}
	
	public function getQualityId() {
		return $this->qualityId;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getCreated() {
		return $this->created;
	}
	
	public function getUpdated() {
		return $this->updated;
	}
}	