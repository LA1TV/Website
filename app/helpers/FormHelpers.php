<?php

use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\UploadPoint;

class FormHelpers {
	
	// stores the extensions an upload point allows with the key being the UploadPoint id
	private static $uploadPointExtensionsCache = array();
	
	public static function getValue($var, $default=null, $useDefault=false, $useGet=false) {
		return $useDefault || !self::hasPost($var, $useGet) ? $default : (!$useGet ? $_POST[$var] : $_GET[$var]);
	}
	
	public static function hasPost($var, $useGet=false) {
		return !$useGet ? isset($_POST[$var]) : isset($_GET[$var]);
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
	
	// return NULL if string empty
	public static function nullIfEmpty($str) {
		return !empty($str) ? $str : null;
	}
	
	public static function toBoolean($val) {
		return $val === "y";
	}
	
	public static function getLessThanCharactersMsg($no) {
		return "This must be less than ".$no." characters.";
	}
	
	public static function getInvalidFileMsg() {
		return "An error occurred with this file.";
	}
	
	public static function getRequiredMsg() {
		return "This is required.";
	}

	public static function getInvalidTimeMsg() {
		return "This time is invalid.";
	}
	
	public static function getInvalidStreamMsg() {
		return "This stream is invalid.";
	}
	
	// checks if the file exists in the database
	// If the file is not in use it checks that the session id matches the session that created it
	public static function getValidFileValidatorFunction() {
		return function($attribute, $value, $parameters) {
			if ($value === "") {
				return true;
			}
			$value = intval($value, 10);
			$file = File::find($value);
			
			return !is_null($file) && ($file->in_use || (!is_null($file->session_id) && $file->session_id === Session::getId()));
		};
	}
	
	public static function getValidStreamValidatorFunction() {
		return function($attribute, $value, $parameters) {
			if ($value === "") {
				return true;
			}
			$value = intval($value, 10);
			$liveStream = LiveStream::find($value);
			return !is_null($liveStream);
		};
	}
	
	public static function getValidDateValidatorFunction() {
		return function($attribute, $value, $parameters) {
			if ($value === "") {
				return true;
			}
			return strtotime($value) !== FALSE && preg_match("/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2}(\.\d+)?)?$/", $value) === 1;
		};
	}
	
	public static function getErrCSS($errors, $name) {
		$error = false;
		if (!is_null($errors)) {
			$error = $errors->has($name);
		}
		return $error ? "has-error" : "";
	}
	
	public static function getErrMsgHTML($errors, $name) {
		$error = false;
		if (!is_null($errors)) {
			$error = $errors->has($name);
		}
		if (!$error) {
			return "";
		}
		$msgs = $errors->get($name);
		$msg = $msgs[0];
		return '<span class="help-block">'.e($msg).'</span>';
	}
	
	public static function getFormUploadInput($formId, $uploadPointId, $txt, $name, $val, $formErrors, $fileName, $fileSize, $remoteRemove, $processState, $processPercentage, $processMsg) {
		return self::getFormGroupStart($name, $formErrors).'<label class="control-label">'.e($txt).'</label>'.self::getFileUploadElement($formId, $name, $uploadPointId, $fileName, $fileSize, $val, $remoteRemove, $processState, $processPercentage, $processMsg).FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getFileUploadElement($formId, $formInputName, $uploadPointId, $currentFileName, $currentFileSize, $value, $remoteRemove, $processState, $processPercentage, $processMsg) {
		$extensions = FormHelpers::getUploadPointExtensions($uploadPointId);
		$remoteRemoveVal = $remoteRemove?"1":"0";
		return '<div class="form-control default-ajax-upload" data-ajaxuploadresultname="'.e($formInputName).'" data-ajaxuploadextensions="'.e(implode(",", $extensions)).'" data-ajaxuploadcurrentfilename="'.e($currentFileName).'" data-ajaxuploadcurrentfilesize="'.e($currentFileSize).'" data-ajaxuploaduploadpointid="'.e($uploadPointId).'" data-ajaxuploadremoteremove="'.e($remoteRemoveVal).'" data-ajaxuploadprocessstate="'.e($processState).'" data-ajaxuploadprocesspercentage="'.e($processPercentage).'" data-ajaxuploadprocessmsg="'.e($processMsg).'"></div>'.self::getFormHiddenInput($formId, $formInputName, $value);
	}
	
	public static function getFormGroupStart($name, $formErrors) {
		return '<div class="form-group '.FormHelpers::getErrCSS($formErrors, $name).'">';
	}
	
	public static function getFormCheckInput($formId, $txt, $name, $enabled, $formErrors) {
		$enabledTxt = $enabled ? "checked":"";
		return self::getFormGroupStart($name, $formErrors).'<div class="checkbox"><label><input type="checkbox" data-virtualform="'.e($formId).'" name="'.e($name).'" value="y" '.$enabledTxt.'> '.e($txt).'</label></div>'.FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getFormTxtInput($formId, $txt, $name, $val, $formErrors, $type="text") {
		$tmp = "";
		if ($type === "datetime-local" || $type === "datetime") {
			$tmp = " step=60";
		}
		return self::getFormGroupStart($name, $formErrors).'<label class="control-label">'.e($txt).'</label><input type="'.$type.'" data-virtualform="'.e($formId).'" class="form-control" name="'.e($name).'" value="'.e($val).'" '.$tmp.'>'.FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getFormDateInput($formId, $txt, $name, $val, $formErrors) {
		return self::getFormTxtInput($formId, $txt, $name, $val, $formErrors, "datetime-local");
	}
	
	public static function getFormPassInput($formId, $txt, $name, $val, $formErrors) {
		return self::getFormTxtInput($formId, $txt, $name, $val, $formErrors, "password");
	}
	
	public static function getFormTxtAreaInput($formId, $txt, $name, $val, $formErrors) {
		return self::getFormGroupStart($name, $formErrors).'<label class="control-label">'.e($txt).'</label><textarea data-virtualform="'.e($formId).'" class="form-control" name="'.e($name).'">'.e($val).'</textarea>'.FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getFormSelectInput($formId, $txt, $name, $val, $options, $formErrors) {
		$selectStr = '<select class="form-control" data-virtualform="'.e($formId).'" name="'.e($name).'">';
		foreach($options as $a) {
			$selectedTxt = $a['id'] == $val ? "selected" : "";
			$selectStr .= '<option value="'.e($a['id']).'" '.$selectedTxt.'>'.e($a['name']).'</option>';
		}
		$selectStr .= '</select>';
	
		return self::getFormGroupStart($name, $formErrors).'<label class="control-label">'.e($txt).'</label>'.$selectStr.FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getFormPageSelectionBar($currentPage, $noPages) {
		$a = '<div class="clearfix">';
		$a .= '<ul class="pagination pull-right">';
		$tmp = $currentPage <= 0 ? 'class="disabled"' : '';
		$tmp2 = $currentPage > 0 ? '<a href="'.URL::current().self::buildGetUri(array("pg"=>$currentPage-1+1)).'">&laquo;</a>' : '<span>&laquo;</span>';
		$a .= '<li '.$tmp.'>'.$tmp2.'</li>';
		for ($i=0; $i<$noPages; $i++) {
			$tmp = $i===$currentPage ? 'class="active"':'';
			$a.= '<li '. $tmp .'><a href="'. URL::current().self::buildGetUri(array("pg"=>$i+1)).'">'.e($i+1).'</a></li>';
		}
		$tmp = $currentPage >= $noPages-1 ? 'class="disabled"' : '';
		$tmp2 = $currentPage < $noPages-1 ? '<a href="'.URL::current().self::buildGetUri(array("pg"=>$currentPage+1+1)).'">&raquo;</a>' : '<span>&raquo;</span>';
		$a .= '<li '.$tmp.'>'.$tmp2.'</li>';
		$a .= '</div>';
		return $a;
	}
	
	public static function getAjaxSelectInput($formId, $txt, $name, $val, $formErrors, $dataUri, $chosenItemText) {
		return self::getFormGroupStart($name, $formErrors).'<label class="control-label">'.e($txt).'</label>'.self::getAjaxSelectElement($formId, $name, $val, $dataUri, $chosenItemText).FormHelpers::getErrMsgHTML($formErrors, $name).'</div>';
	}
	
	public static function getAjaxSelectElement($formId, $formInputName, $value, $dataUri, $chosenItemTxt) {		
		return '<div class="form-control default-ajax-select" data-datasourceuri="'.e($dataUri).'" data-destinationname="'.e($formInputName).'" data-chosenitemtext="'.e($chosenItemTxt).'"></div>'.self::getFormHiddenInput($formId, $formInputName, $value);
	}
	
	public static function getSearchBar() {
		return '<div class="search-box clearfix"><div class="the-container"><input type="text" class="form-control search-input" placeholder="Search" value="'.self::getValue("search", "", false, true).'"></div></div>';
	}

	public static function getFormHiddenInput($formId, $name, $val) {
		return '<input type="hidden" data-virtualform="'.e($formId).'" name="'.e($name).'" value="'.e($val).'">';
	}
	
	public static function getFormSubmitButton($formId, $val, $action="", $primary=false, $confirmMsg=null, $method="post") {
		if (empty($action)) {
			$action = Request::url();
		}
		$tmp = $primary ? " btn-primary" : " btn-default";
		$tmp2 = "";
		if (!is_null($confirmMsg)) {
			$tmp2 = ' data-virtualformconfirm="'.e($confirmMsg).'"';
		}
		return '<button type="button" data-virtualform="'.e($formId).'" data-virtualformsubmit="1" data-virtualformsubmitmethod="'.e($method).'" data-virtualformsubmitaction="'.e($action).'"'.$tmp2.' class="btn'.e($tmp).'">'.e($val).'</button>';
	}
	
	public static function getPageNo() {
		$no = intval(self::getValue("pg", 1, false, true), 10)-1;
		return $no < 0 ? 0 : $no;
	}
	
	public static function getPageStartIndex() {
		return self::getPageNo() * self::getPageNoItems();
	}
	
	public static function getPageNoItems() {
		return Config::get("custom.items_per_page");
	}
	
	public static function getNoPages($noItems) {
		return ceil($noItems / self::getPageNoItems());
	}
	
	// generate query string with any attributes in uri still set unless overridden in $attrs
	public static function buildGetUri($attrs) {

		foreach($_GET as $b=>$a) {
			if (!array_key_exists($b, $attrs)) {
				$attrs[$b] = $a;
			}
		}
	
		$uri = "?";
		$first = TRUE;
		foreach($attrs as $b=>$a) {
			if (!$first) {
				$uri .= "&amp;";
			}
			else {
				$first = FALSE;
			}
			$uri .= urlencode($b)."=".urlencode($a);
		}
		return $uri;
	}
	
	public static function getUploadPointExtensions($uploadPointId) {
		
		if (in_array($uploadPointId, self::$uploadPointExtensionsCache)) {
			return self::$uploadPointExtensionsCache[$uploadPointId];
		}
		$a = UploadPoint::with("fileType", "fileType.extensions")->findOrFail($uploadPointId);
		$extensions = $a->fileType->getExtensionsArray();
		// add to cache
		self::$uploadPointExtensionsCache[$a->id] = $extensions;
		return $extensions;
	}
	
	public static function getFileInfo($fileId) {
		$info = array(
			"name"	=> "",
			"size"	=> "",
			"processState"	=> "0",
			"processPercentage"	=> "",
			"processMsg"	=> ""
		);
		if ($fileId === "") {
			return $info;
		}
		
		$file = File::find(intval($fileId, 10));
		if (!is_null($file)) {
			$info['name'] = $file->filename;
			$info['size'] = $file->size;
			$info['processState'] = $file->process_state;
			$info['processPercentage'] = !is_null($file->process_percentage) ? $file->process_percentage : "";
			$info['processMsg'] = !is_null($file->msg) ? $file->msg : "";
		}
		return $info;
	}
	
	public static function formatDateForInput($timestamp) {
		return date("Y-m-d\TH:i", $timestamp);
	}
}