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
	
	public static function getLessThanCharactersMsg($no) {
		return "This must be less than ".$no." characters.";
	}
	
	public static function getInvalidFileMsg() {
		return "This file type is not allowed.";
	}
	
	public static function getRequiredMsg() {
		return "This is required.";
	}

	public static function getInvalidTimeMsg() {
		return "This time is invalid.";
	}
	
	public static function getValidFileValidatorFunction() {
		return function($attribute, $value, $parameters) {
			if ($value === "") {
				return true;
			}
			$value = intval($value, 10);
			$file = File::find($value);
			return !(is_null($file) || $file->in_use || is_null($file->session_id) || $file->session_id !== Session::getId() || !in_array($file->getExtension(), explode("-", $parameters[0]), true));
		};
	}
	
	public static function getFileUploadElement($formName, $extensions, $currentFileName, $currentFileSize, $value) {
		return '<div class="form-control ajax-upload" data-ajaxuploadresultname="'.e($formName).'" data-ajaxuploadextensions="'.e(implode(",", $extensions)).'" data-ajaxuploadcurrentfilename="'.e($currentFileName).'" data-ajaxuploadcurrentfilesize="'.e($currentFileSize).'"></div><input type="hidden" data-virtualform="1" name="'.e($formName).'" value="'.e($value).'" />';
	}
}