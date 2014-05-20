<?php namespace uk\co\la1tv\website\models;

class LiveStream {

	private $id;
	private $name;
	private $description;
	private $loadBalancerServerAddress;
	private $serverAddress;
	private $dvrEnabled;
	private $created;
	private $updated;
	private $qualities;
	
	public function Stream($id, $name, $description, $loadBalancerServerAddress, $serverAddress, $dvrEnabled, $created, $updated, $qualities) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->loadBalancerServerAddress = $loadBalancerServerAddress;
		$this->serverAddress = $serverAddress;
		$this->dvrEnabled = $dvrEnabled;
		$this->created = $created;
		$this->updated = $updated;
		$this->qualities = $qualities;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getIsUsingLoadBalancer() {
		return $this->serverAddress === NULL;
	}

	public function getLoadBalancerServerAddress() {
		if (!$this->getIsUsingLoadBalancer()) {
			throw(new Exception("Load balancer not in use."));
		}
		return $this->loadBalancerServerAddress;
	}
	
	public function getServerAddress() {
		if ($this->getIsUsingLoadBalancer()) {
			throw(new Exception("Load balancer in use."));
		}
		return $this->serverAddress;
	}
	
	public function getDvrEnabled() {
		return $this->dvrEnabled;
	}
	
	public function getCreated() {
		return $this->created;
	}
	
	public function getUpdated() {
		return $this->updated;
	}

	public function getQualities() {
		return $this->qualities;
	}
}