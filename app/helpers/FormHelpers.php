<?php

class FormHelpers {
	
	public static function getValue($var, $default, $useDefault) {
		return $useDefault || !self::hasPost($var) ? $default : $_POST[$var];
	}
	
	public static function hasPost($var) {
		return isset($_POST[$var]);
	}
	
	// $structure should be an array containing arrays of form
	// array(0=>[id], 1=>[default val])
	// returns array where key is id and value is value
	public static function getFormData($structure, $useDefault) {
		
		$formData = array();
		
		foreach($structure as $a) {
			$formData[$a[0]] = self::getValue($a[0], $a[1], $useDefault);
		}
		
		return $formData;
	}

}