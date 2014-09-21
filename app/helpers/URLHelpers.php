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

}