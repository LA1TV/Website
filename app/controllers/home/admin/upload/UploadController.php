<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use uk\co\la1tv\website\models\File;

class UploadController extends UploadBaseController {

	public function postIndex() {
		
		// A list of permitted file extensions
		$extensions = array('jpg', 'jpeg', 'mp4');
		$maxFileLength = 50;
		
		$resp = array("success"=> false);
		
		if (isset($_FILES['files']) && count($_FILES['files']['name']) >= 1 && strlen($_FILES['files']['name'][0]) <= $maxFileLength) {
			
			$fileLocation = $_FILES['files']['tmp_name'][0];
			$fileName = $_FILES['files']['name'][0];
			$fileSize = filesize($fileLocation);
			
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			if (in_array($extension, $extensions) && $fileSize != FALSE && $fileSize > 0) {
				
				// create the file reference in the db
				$fileDb = File::create(array(
					"in_use"	=> false,
					"filename"	=> $fileName,
					"filesize"	=> $fileSize,
					"session_id"	=> Session::getId() // the laravel session id
				));
			
				// TODO: move the file
				$resp['success'] = true;
				$resp['id'] = 0;
				$resp['filename'] = $fileName;
				$resp['filesize'] = $fileSize;
			}
		}
		
		return Response::json($resp);
	}
	
	// get information about a temporary file
	public function postInfo($id) {
		$resp = array("success"=> false);
		// TODO
		return Response::json($resp);
	}
	
	// remove a temporary file
	public function postRemove($id) {
		$resp = array("success"=> false);
		// TODO
		return Response::json($resp);
	}
}
