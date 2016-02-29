<?php namespace uk\co\la1tv\website\jobs;

use DB;
use DebugHelpers;
use Log;
use Config;
use uk\co\la1tv\website\models\File;

class TransferUploadJob {
	
	// responsible for sending emails saying a mediaitem has gone live.
	// expects data to contain "mediaItemId" of the media item that has just gone live
	public function fire($job, $data) {	
	

		if (!DebugHelpers::shouldSiteBeLive()) {
			$job->release(); // put the job back on the queue
			return;
		}
		
		// remove the job from the queue to make sure it only runs once even if an exception is thrown
		$job->delete();

		$fileId = $data['fileId'];
		// the path to the file to be moved to the file store
		$filePath = $data['filePath'];

		Log::info("Starting job to move upload with id ".$fileId." to files location.");
		
		// transaction to make sure the session doesn't become null on the model (which would result in the upload processor trying to delete it, and failing silently if it can't find the file) whilst the file is being moved.
		$success = DB::transaction(function() use (&$fileId, &$filePath) {
			
			$fileDb = File::find($fileId);
			if (is_null($fileDb)) {
				throw(new Exception("File model has been deleted!"));
			}

			if (is_null($fileDb->session_id)) {
				// source file should get deleted after a while because the session id
				// is contained in the file name, and the system will realise it belongs
				// to no one now.
				throw(new Exception("The session that was used when the file was uploaded no longer exists."));
			}

			// move the file providing the file record created successfully.
			// it is important there's always a file record for each file.
			$moveSuccesful = self::moveFile($filePath, Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $fileDb->id);
			
			if ($moveSuccesful) {
				// if there is a failure before the ready_for_processing flag is set then it is possible for there to either be a file which will never be removed automatically, or no file for this record. I think this is the only place in the entire system where there could be an error which would require manual attention.
				// set ready_for_processing to true so that processing can start.
				$fileDb->ready_for_processing = true;
				$fileDb->save();
				return true;
			}
			return false;
		});

		if (!$success) {
			Log::error("An error occurred trying to move upload with id ".$fileId." to files location.");
		}

		Log::info("Finished job to move upload with id ".$fileId." to files location.");
	}
	

	// rename() is unreliable when the file may be being moved accross volumes
	// this does a copy operation and then a delete instead
	// if the source file fails to be be deleted TRUE will still be returned
	private static function moveFile($src, $dest) {
		if (copy($src, $dest)) {
			// now delete the source
			unlink($src);
			return true;
		}
		return false;
	}
}