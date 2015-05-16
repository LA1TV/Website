<?php

class MyResponse extends Illuminate\Http\Response {
	
	private $contentSecurityPolicyEnabled = true;
	private $contentSecurityPolicyDomains = array();
	
	public function enableContentSecurityPolicy($enable) {
		$this->contentSecurityPolicyEnabled = (boolean) $enable;
	}
	
	public function isContentSecurityPolicyEnabled() {
		return $this->contentSecurityPolicyEnabled;
	}
	
	public function setContentSecurityPolicyDomains($domains) {
		$this->contentSecurityPolicyDomains = $domains;
	}
	
	public function getContentSecurityPolicyDomains() {
		return $this->contentSecurityPolicyDomains;
	}
	
}