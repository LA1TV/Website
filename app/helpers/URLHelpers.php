<?php

class URLHelpers {
	
	public static function escapeAndReplaceUrls($text) {
		// based on http://stackoverflow.com/a/1945957/1048589
		
		// a more readably-formatted version of the pattern is on http://daringfireball.net/2010/07/improved_regex_for_matching_urls
		$pattern  = '/(?<text>.*?)(\b(?<url>(?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])))?/i';
		
		$outputText = "";
		$matches = null;
		if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER) !== false) {
			foreach($matches as $match) {
				if (isset($match['text'])) {
					$outputText .= e($match['text']);
				}
				if (isset($match['url'])) {
					if (filter_var($match['url'], FILTER_VALIDATE_URL) !== false) {
						$outputText .= sprintf('<a target="_blank" href="%s">%s</a>', e($match['url']), e($match['url']));
					}
					else {
						// even though the regex matches, ie thinks this is a url, php thinks differently, so treat it as not being one.
						$outputText .= e($match['url']);
					}
				}
			}
		}
		return $outputText;
	}
	
	// returns the url if the referrer is set or NULL otherwise.
	public static function getReferrerUrl() {
		$a = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		return !empty($a) ? $a : null;
	}
	
	// true if the user came here from a link on the site, false if they came from an external address.
	public static function hasInternalReferrer() {
		$url = self::getReferrerUrl();
		if (is_null($url)) {
			return false;
		}
		return self::isUrlOnSameDomain($url);
	}
	
	// returns true if the provided url has the same domain as the server
	public static function isUrlOnSameDomain($url) {
		$urlParts = parse_url($url);
		$localUrlParts = parse_url(URL::to("/"));
		if ($urlParts === false || $localUrlParts === false) {
			return false;
		}
		
		if (!isset($urlParts['scheme']) || !isset($localUrlParts['scheme']) ||
			!isset($urlParts['host']) || !isset($localUrlParts['host'])) {
			return false;
		}
		
		$urlPort = !isset($urlParts['port']) ? 80 : $urlParts['port'];
		$localUrlPort = !isset($localUrlParts['port']) ? 80 : $localUrlParts['port'];
			
		return $urlParts['scheme'] === $localUrlParts['scheme'] && $urlParts['host'] === $localUrlParts['host'] && $urlPort === $localUrlPort;
	}
	
	public static function getPath() {
		return implode("/", Request::segments());
	}
	
	public static function generateLoginUrl() {
		return Config::get("custom.base_url") . "/facebook/login?returnuri=".urlencode(URLHelpers::getPath());
	}
	
	public static function generateLogoutUrl() {
		return Config::get("custom.base_url") . "/facebook/logout?returnuri=".urlencode(URLHelpers::getPath());
	}
}