<?php

class MyResponse extends Illuminate\Http\Response {
	
	private $contentSecurityPolicyDomains = array();
	
	public function setContentSecurityPolicyDomains($domains) {
		$this->contentSecurityPolicyDomains = $domains;
	}
	
	public function getContentSecurityPolicyDomains() {
		return $this->contentSecurityPolicyDomains;
	}
	
}