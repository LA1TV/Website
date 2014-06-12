<?php namespace uk\co\la1tv\website\serviceProviders\upload;

use Response;
use Session;
use Config;
use DB;
use uk\co\la1tv\website\models\UploadPoint;

class UploadManager {

	private static $maxFileLength = 50; // length of varchar in db
	
	private $processCalled = false;
	private $responseData = array();
	
	// process the file that has been uploaded
	// pass in the UploadPoint model corresponding to the point where this file was uploaded from
	// When loading the model eager load it with fileType and fileType.extensions as these are needed.
	// Returns true if succeeds or false otherwise
	public function process(UploadPoint $uploadPoint) {
		
		if ($this->processCalled) {
			throw(new Exception("'process' can only be called once."));
		}
		$processCalled = true;
		
		$this->responseData = array("success"=> false);
		$success = false;
		
		if (isset($_FILES['files']) && count($_FILES['files']['name']) >= 1 && strlen($_FILES['files']['name'][0]) <= self::$maxFileLength && isset($_FILES['files']['tmp_name'][0])) {
			
			$fileLocation = $_FILES['files']['tmp_name'][0];
			$fileName = $_FILES['files']['name'][0];
			$fileSize = filesize($fileLocation);
			
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			$extensions = array();
			foreach($uploadPoint->extensions as $a) {
				$extensions[] = $a->extension;
			}
			if (in_array($extension, $extensions) && $fileSize != FALSE && $fileSize > 0) {

				try {
					DB::beginTransaction();
					
					// create the file reference in the db
					$fileDb = new File(array(
						"in_use"	=> false,
						"filename"	=> $fileName,
						"size"		=> $fileSize,
						"session_id"	=> Session::getId() // the laravel session id
					));
					$fileDb->fileType()->associate($uploadPoint->fileType);
					
					if ($fileDb->save() !== FALSE) {
						// move the file
						if (move_uploaded_file($fileLocation, Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $fileDb->id)) {				
							
							// commit transaction so file record is committed to database
							DB::commit();
							
							// success
							$success = true;
							$this->responseData['success'] = true;
							$this->responseData['id'] = $fileDb->id;
							$this->responseData['fileName'] = $fileName;
							$this->responseData['fileSize'] = $fileSize;
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
				return $success;
			}
		}
	}
	
	// get the Laravel response (json) object to be returned to the user
	public function getResponse() {
		if (!$this->processCalled) {
			throw(new Exception("'process' must have been called first."));
		}
		return Response::json($this->responseData);
	}
	
	// get an array containing information about the last upload
	// returns array or null if there was an error processing
	public function getInfo() {
		if (!$this->processCalled) {
			throw(new Exception("'process' must have been called first."));
		}
		$data = $this->responseData;
		return $data['success'] ? array("fileName"=>$data['fileName'], "fileSize"=>$data['fileSize']) : null;
	}
	
}