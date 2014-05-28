<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use Config;
use App;
use DB;
use Input;
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
						"size"		=> $fileSize,
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
							$resp['fileName'] = $fileName;
							$resp['fileSize'] = $fileSize;
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
	public function postInfo() {
		$resp = array("success"=> false);
		if (Input::has("id")) {
			$id = intval(Input::get("id"), 10);
			$file = $this->getFile($id);
			if (!is_null($file)) {
				$resp['fileName'] = $file->filename;
				$resp['fileSize'] = $file->size;
				$resp['success'] = true;
			}
		}
		return Response::json($resp);
	}
	
	// remove a temporary file
	public function postRemove() {
		$resp = array("success"=> false);
		if (Input::has("id")) {
			$id = intval(Input::get("id"), 10);
			$file = $this->getFile($id);
			if (!is_null($file)) {
				if (unlink(Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $file->id)) {
					$file->delete();
					$resp['success'] = true;
				}
			}
		}
		return Response::json($resp);
	}
	
	// get file model from id if security checks pass
	private function getFile($id) {
		$file = File::find($id);
		if (!is_null($file)) {
		
			// check that the file isn't in_use (so temporary) and the session_id matches this users session
			if (!$file->in_use && $file->session_id === Session::getId()) {
				return $file;
			}
		}
		return null;
	}
}
