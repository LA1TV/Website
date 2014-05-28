<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use Config;
use App;
use DB;
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
				
				try {
					DB::beginTransaction();
					
					// create the file reference in the db
					$fileDb = File::create(array(
						"in_use"	=> false,
						"filename"	=> $fileName,
						"filesize"	=> $fileSize,
						"session_id"	=> Session::getId() // the laravel session id
					));
					
					if (!is_null($fileDb)) {
						// move the file
						if (move_uploaded_file($fileLocation, Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $fileDb->id)) {				
							
							// commit transaction so file record is committed to database
							DB::commit();
							
							// create the response
							$resp['success'] = true;
							$resp['id'] = $fileDb->id;
							$resp['filename'] = $fileName;
							$resp['filesize'] = $fileSize;
						}
						else {
							DB::rollback();
						}
					}
					else {
						DB::rollback();
					}
				}
				catch (\Exception $e) {
					DB::rollback();
					throw($e);
				}
			}
		}
		
		return Response::json($resp);
	}
	
	// serve up a file
	public function getIndex($id) {
		App::abort(403); // forbidden
		
		App::abort(404);
		
		
		dd(Config::get("custom.files_location"));
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
