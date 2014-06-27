<?php namespace uk\co\la1tv\website\fileObjs;

use uk\co\la1tv\website\models\File;
use Exception;

abstract class FileObj {

	private $file = null;
	private $constructed = false;

	public final function __construct(File $file) {
		if ($this->constructed) {
			throw(new Exception("This must only be constructed once from the FileObjBuilder."));
		}
		$this->constructed = true;
		$this->file = $file;
		$this->init();
	}
	
	// get the File model this is related to
	public final function getFile() {
		return $this->file;
	}
	
	// called at end of constructor.
	public function init() {
		return;
	}
	
	// all of the callbacks, pre and post, are called from inside a db transaction
	// this means any changes made here might be rolled back if the callee fails.
	// eg if a record is created in the db somewhere in the preRegistration callback but then an exception is thrown later and the file cannot be registered for some reason, these changes will not be reflected in the db.
	
	// called immediately after the File model is created
	public function postCreation() {
		return;
	}
	
	// called immediately before the file is registered (i.e in_use set to 1)
	// return false to cancel the registration or true to allow it.
	public function preRegistration() {
		return true;
	}
	
	// called immediately before after file is registered
	public function postRegistration() {
		return;
	}
	
	// called immediately before the file is marked for deletion
	// return false to cancel the deletion or true to allow it.
	public function preDeletion() {
		return true;
	}
	
	// called immediately after the file has been marked for deletion
	public function postDeletion() {
		return;
	}
}