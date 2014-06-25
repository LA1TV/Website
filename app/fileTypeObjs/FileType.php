<?php namespace uk\co\la1tv\website\fileTypeObjs;

use uk\co\la1tv\website\models\File;

abstract class FileTypeObj {
	
	// all of the callbacks are called from inside a db transaction
	// this means any changes made here might be rolled back if the callee fails.
	// eg if a record is created in the db somewhere in the preRegistration callback but then an exception is thrown later and the file cannot be registered for some reason, these changes will not be reflected in the db.
	
	// called immediately before the File model is saved
	// return false to cancel the saving or true to allow it.
	public function preCreation(File $f) {
		return true;
	}
	
	// called immediately before the file is registered (i.e in_use set to 1)
	// return false to cancel the registration or true to allow it.
	public function preRegistration(File $f) {
		return true;
	}
	
	// called immediately before the file is marked for deletion
	// return false to cancel the deletion or true to allow it.
	public function preDeletion(File $f) {
		return true;
	}
	
	// return the process stage that this file is at
	public function processStage(File $f) {
		return 0;
	}
	
	// return true if an error has occurred with this file
	public function hasError(File $f) {
		return false;
	}
}