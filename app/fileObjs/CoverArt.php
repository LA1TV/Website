<?php namespace uk\co\la1tv\website\fileObjs;



class CoverArt extends FileObj {
	public function postRegistration() {
		// TODO: Create entry in cover_art_files with the source file id from $this->getFile() so that processing can start
		return;
	}
	
	public function preDeletion() {
		return true;
	}
}