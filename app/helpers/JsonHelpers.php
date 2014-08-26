<?php

class JsonHelpers {
	
	public static function jsonDecodeOrNull($str, $assoc=false, $depth=512) {
		if (!is_string($str)) {
			return null;
		}
		return json_decode($str, $assoc, $depth);
	}
}