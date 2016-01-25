<?php

class MyResponse extends Illuminate\Http\Response {
	
	// cannot be enabled at the moment as the site puts the page config as an inline
	// script now for performance. A hash of this needs adding to the script tag
	// and the appropriate header in order for this to be possible again
	private $contentSecurityPolicyEnabled = false;
	private $contentSecurityPolicyDomains = array();
	
	public function enableContentSecurityPolicy($enable) {
		if ($enable) {
			throw(new Exception("This cannot be enabled at the moment."));
		}
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