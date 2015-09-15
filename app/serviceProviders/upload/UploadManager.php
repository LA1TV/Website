<?php namespace uk\co\la1tv\website\serviceProviders\upload;

use Response;
use Redirect;
use Session;
use Config;
use DB;
use FormHelpers;
use Exception;
use Csrf;
use EloquentHelpers;
use FileHelpers;
use Auth;
use uk\co\la1tv\website\models\UploadPoint;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\OldFileId;
use uk\co\la1tv\website\models\Session as SessionModel;

class UploadManager {

	private static $maxFileLength = 50; // length of varchar in db
	
	private $processCalled = false;
	private $responseData = array();
	
	// process the file that has been uploaded
	// Returns true if succeeds or false otherwise
	// The file may be a chunk of the complete file in which case this just puts it to one side/builds the file with the new chunks.
	// When the last chunk arrives it will then create the db etc.
	public function process($allowedIds=null) {
		
		if ($this->processCalled) {
			throw(new Exception("'process' can only be called once."));
		}
		$this->processCalled = true;
		
		$this->responseData = array("success"=> false);
		$success = false;
		
		$info = $this->buildFile();
		if (!$info['success']) {
			$success = false;
			$this->responseData['success'] = false;
			$this->responseData['wasChunk'] = true;
		}
		else if (is_null($info['info'])) {
			$success = true;
			$this->responseData['success'] = true;
			$this->responseData['wasChunk'] = true;
		}
		else {
			$fileInfo = $info['info'];
			$uploadPointId = FormHelpers::getValue("upload_point_id");
			
			if (Csrf::hasValidToken() && !is_null($uploadPointId) && (is_null($allowedIds) || in_array($uploadPointId, $allowedIds, true))) {
				
				$uploadPointId = intval($uploadPointId, 10);
				$uploadPoint = UploadPoint::with("fileType", "fileType.extensions")->find($uploadPointId);
				
				if (!is_null($uploadPoint) && strlen($fileInfo['name']) <= self::$maxFileLength) {
					
					$fileLocation = $fileInfo['path'];
					$fileName = $fileInfo['name'];
					$fileSize = FileHelpers::filesize64($fileLocation);
					
					$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
					$extensions = array();
					$extensionModels = $uploadPoint->fileType->extensions;
					if (!is_null($extensionModels)) {
						foreach($extensionModels as $a) {
							$extensions[] = $a->extension;
						}
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
							$fileDb->uploadPoint()->associate($uploadPoint);
							if ($fileDb->save() !== FALSE) {
								
								// commit transaction so file record is committed to database
								DB::commit();
								
								DB::beginTransaction();
								// another transaction to make sure the session doesn't become null on the model (which would result in the upload processor trying to delete it, and failing silently if it can't find the file) whilst the file is being moved.
								$fileDb = File::find($fileDb->id);
								if (is_null($fileDb)) {
									throw(new Exception("File model has been deleted!"));
								}
								if ($fileDb->session_id !== Session::getId()) {
									throw(new Exception("Session has changed between transactions!"));
								}
								// move the file providing the file record created successfully.
								// it is important there's always a file record for each file.
								if (rename($fileLocation, Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $fileDb->id)) {
									// set ready_for_processing to true so that processing can start.
									$fileDb->ready_for_processing = true;
									$fileDb->save();
									DB::commit();
									
									// if there is a failure before the ready_for_processing flag is set then it is possible for there to either be a file which will never be removed automatically, or no file for this record. I think this is the only place in the entire system where there could be an error which would require manual attention.
									
									// success
									$success = true;
									$this->responseData['success'] = true;
									$this->responseData['id'] = $fileDb->id;
									$this->responseData['fileName'] = $fileName;
									$this->responseData['fileSize'] = $fileSize;
									$this->responseData['processInfo'] = $fileDb->getProcessInfo();
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
			}
		}
		return $success;
	}
	
	// buildFile will append the current file chunk to the stored chunks.
	// if this is the last chunk and there's now a complete file it returns the info about the completed file, otherwise the info key is null, meaning there's more chunks left to come in
	// the return value an array of form array("success", "info"=>array("name", "path")) where "name" is the files original name and "path" is the path to the built file. success is false if there was an error with the current chunk.
	// it names the files as [session_id]-[fileid]-[original name]. this means when a users session expires any incomplete chunks can be removed easily
	private function buildFile() {
		
		$this->clearTempChunks();
	
		$returnVal = array("success" => false, "info" => null);
		
		// http://www.plupload.com/docs/Chunking
		if (!empty($_FILES) && is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES['file']['error'] === 0) {
			$chunk = isset($_POST["chunk"]) ? intval($_POST["chunk"]) : 0;
			$chunks = isset($_POST["chunks"]) ? intval($_POST["chunks"]) : 0;

			if (isset($_POST['id']) && ctype_digit($_POST['id'])) {
				$fileId = intval($_POST['id']);
				$actualFileName = isset($_POST['name']) ? $_POST["name"] : $_FILES["file"]["name"];
				$fileName = Session::getId()."-".$fileId."-".$actualFileName;
				$filePath = Config::get("custom.file_chunks_location") . DIRECTORY_SEPARATOR . $fileName;
				
				// Open temp file
				$out = @fopen($filePath.".part", $chunk === 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen($_FILES['file']['tmp_name'], "rb");
					if ($in) {
						while ($buff = fread($in, 4096)) {
							fwrite($out, $buff);
						}
						@fclose($in);
						@fclose($out);
						$returnVal['success'] = true;
						// Check if the complete file has now been uploaded
						if ($chunks === 0 || $chunk === $chunks - 1) {
							// Strip the temp .part suffix off
							rename($filePath.".part", $filePath);
							$returnVal['info'] = array(
								"name"	=> $actualFileName,
								"path"	=> $filePath
							);
						}
					}
					else {
						@fclose($in);
						@fclose($out);
					}
				}
				else {
					@fclose($out);
				}
				@unlink($_FILES['file']['tmp_name']);
			}
		}	
		return $returnVal;
	}
	
	// removes any files that no longer belong to a session
	private function clearTempChunks() {
		$sessionModels = SessionModel::get();
		$sessionIds = array();
		foreach($sessionModels as $a) {
			$sessionIds[] = $a->id;
		}
		
		foreach (scandir(Config::get("custom.file_chunks_location")) as $filename) {
			if ($filename !== "." && $filename !== "..") {
				$parts = explode("-", $filename);
				if (count($parts) >= 2) {
					$sessionId = $parts[0];
					if (!in_array($sessionId, $sessionIds, true)) {
						// the session that created this file has expired. remove the file
						unlink(Config::get("custom.file_chunks_location") . DIRECTORY_SEPARATOR . $filename);
					}
				}
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
	
	// register a file as now in use by its id. It assumed that this id is valid. an exception is thrown otherwise
	// if the file has already been registered then an exception is thrown, unless the $fileToReplace is the same file.
	// the first parameter is the upload point id and this is used to check that the file being registered is one that was uploaded at the expected upload point
	// optionally pass in the File object of a file that this will be replacing.
	// returns the File model of the registered file or null if $fileId was null
	// if the $fileId is null then the $fileToReplace will be removed and null will be returned.
	public static function register($uploadPointId, $fileId, File $fileToReplace=null) {
		
		$uploadPoint = UploadPoint::with("fileType", "fileType.extensions")->find($uploadPointId);	
	
		if (is_null($uploadPoint)) {
			throw(new Exception("Invalid upload point."));
		}
		
		if (!is_null($fileToReplace) && !is_null($fileId) && intval($fileToReplace->id, 10) === intval($fileId, 10)) {
			// if we are replacing the file with the same file then nothing to do.
			// just return the model
			return $fileToReplace;
		}
		
		$file = null;
		if (!is_null($fileId)) {
			$fileId = intval($fileId, 10);
			$file = File::with("uploadPoint")->find($fileId);
			if (is_null($file)) {
				throw(new Exception("File model could not be found."));
			}
			else if (is_null($file->uploadPoint)) {
				throw(new Exception("This file doesn't have an upload point. This probably means it was created externally and 'register' should not be used on it."));
			}
			else if ($file->in_use) {
				throw(new Exception("This file has already been registered."));
			}
			else if ($file->uploadPoint->id !== $uploadPoint->id) {
				throw(new Exception("Upload points don't match. This could happen if a file was uploaded at one upload point and now the file with that id is being registered somewhere else."));
			}
		}
		
		if (!is_null($file)) {
			$file->in_use = true; // mark file as being in_use now
		}
		DB::transaction(function() use (&$file, &$fileToReplace) {
			$oldIds = array();
			// if the old file has old file ids that used to point to it then save them so they can be copied across
			if (!is_null($fileToReplace)) {
				$oldIds = $fileToReplace->oldFileIds()->get()->map(function($a) {
					return intval($a->old_file_id);
				});
				$oldIds[] = intval($fileToReplace->id);
			}
			
			// this must happen before new file is saved (after the old ids that pointed to this file have been retrieved)
			// so that don't end up with duplicate old file ids
			if (!is_null($fileToReplace)) {
				self::delete($fileToReplace);
			}

			if (!is_null($file)) {
				foreach($oldIds as $a) {
					$file->oldFileIds()->save(new OldFileId(array(
						"old_file_id"	=> $a
					)));
				}
				
				if ($file->save() === false) {
					throw(new Exception("Error saving file model."));
				}
			}
		});
		
		return $file;
	}
	
	// mark the files/file past in for deletion
	// will ignore any null models
	public static function delete($files) {
		if (!is_array($files)) {
			$files = array($files);
		}
		foreach($files as $a) {
			if (!is_null($a)) {
				$a->oldFileIds()->delete();
				$a->markReadyForDelete();
				$a->save();
			}
		}
	}
	
	// returns the File object for a file if the security checks pass.
	// returns the File model or null
	public static function getFile($fileId) {
		
		// the file must be a render (ie have a source file) file to be valid. Then the security checks are performed on the source file.

		$relationsToLoad = array(
			"sourceFile",
			"sourceFile.mediaItemWithBanner",
			"sourceFile.mediaItemWithBannerFill",
			"sourceFile.mediaItemWithCover",
			"sourceFile.mediaItemWithCoverArt",
			"sourceFile.playlistWithBanner",
			"sourceFile.playlistWithBannerFill",
			"sourceFile.playlistWithCover",
			"sourceFile.liveStreamWithCoverArt",
			"sourceFile.mediaItemVideoWithFile.mediaItem",
			"sourceFile.videoFileDashWithMediaPresentationDescription",
			"sourceFile.videoFileDashWithAudioChannel",
			"sourceFile.videoFileDashWithVideoChannel"
		);

		$requestedFile = File::with($relationsToLoad)->finishedProcessing()->find($fileId);
		if (is_null($requestedFile)) {
			return null;
		}
		
		$sourceFile = $requestedFile->sourceFile;
		if (is_null($sourceFile)) {
			return null;
		}
		
		$fileType = $requestedFile->file_type;
		$fileTypeId = intval($fileType->id);
		
		$user = Auth::getUser();
		$hasMediaItemsPermission = false;
		$hasPlaylistsPermission = false;
		if (!is_null($user)) {
			$hasMediaItemsPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0);
			$hasPlaylistsPermission = Auth::getUser()->hasPermission(Config::get("permissions.playlists"), 0);
		}
		
		$accessAllowed = false;
		
		// see if the file should be accessible
		if ($fileTypeId === 5 && !is_null($sourceFile->mediaItemWithBanner)) {
			if ($sourceFile->mediaItemWithBanner->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 11 && !is_null($sourceFile->mediaItemWithBannerFill)) {
			if ($sourceFile->mediaItemWithBannerFill->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 6 && !is_null($sourceFile->mediaItemWithCover)) {
			if ($sourceFile->mediaItemWithCover->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 8 && !is_null($sourceFile->mediaItemWithCoverArt)) {
			if ($sourceFile->mediaItemWithCoverArt->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		// file type 9 = video scrub thumbnail,
		// 12 = dash media presentation description files
		// 13 = dash segment file
		// 15 = hls playlist file
		// 16 = hls segment file
		// these should only be accessible if the video itself is
		else if (($fileTypeId === 7 || $fileTypeId === 9 || $fileTypeId === 12 || $fileTypeId === 13 || $fileTypeId === 15 || $fileTypeId === 16) && !is_null($sourceFile->mediaItemVideoWithFile)) {
			if ($sourceFile->mediaItemVideoWithFile->mediaItem->getIsAccessible() && ($sourceFile->mediaItemVideoWithFile->getIsLive() || $hasMediaItemsPermission)) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 5 && !is_null($sourceFile->playlistWithBanner)) {
			if ($sourceFile->playlistWithBanner->getIsAccessible() && ($sourceFile->playlistWithBanner->getIsAccessibleToPublic() || $hasPlaylistsPermission)) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 11 && !is_null($sourceFile->playlistWithBannerFill)) {
			if ($sourceFile->playlistWithBannerFill->getIsAccessible() && ($sourceFile->playlistWithBannerFill->getIsAccessibleToPublic() || $hasPlaylistsPermission)) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 6 && !is_null($sourceFile->playlistWithCover)) {
			if ($sourceFile->playlistWithCover->getIsAccessible() && ($sourceFile->playlistWithCover->getIsAccessibleToPublic() || $hasPlaylistsPermission)) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 8 && !is_null($sourceFile->playlistWithCoverArt)) {
			if ($sourceFile->playlistWithCoverArt->getIsAccessible() && ($sourceFile->playlistWithCoverArt->getIsAccessibleToPublic() || $hasPlaylistsPermission)) {
				$accessAllowed = true;
			}
		}
		else if ($fileTypeId === 8 && !is_null($sourceFile->liveStreamWithCoverArt)) {
			if ($sourceFile->liveStreamWithCoverArt->getShowAsLiveStream()) {
				$accessAllowed = true;
			}
		}
		
		return $accessAllowed ? $requestedFile : null;
	}
	
	// helper that returns true if the current user should have access to this file
	public static function hasAccessToFile($fileId) {
		return !is_null(self::getFile($fileId));
	}
	
	// returns the file laravel response that should be returned to the user.
	// this will either be the file (with cache header to cache for a year), a redirect if the file has been updated, or a 404
	public static function getFileResponse($fileId) {
		$file = self::getFile($fileId);
		if (is_null($file)) {
			// see if the file used to exist, and if it did then return a redirect to the new version
			$oldFileIdModel = OldFileId::where("old_file_id", $fileId)->first();
			$newFileUri = !is_null($oldFileIdModel) ? $oldFileIdModel->newFile->getUri() : null;
			if (!is_null($newFileUri)) {
				// file has moved
				// return permanent redirect
				return Redirect::away($newFileUri, 301);
			}
			else {
				// file doesn't exist (or is not accessible for some reason)
				// return 404 response
				return Response::make("", 404);
			}
		}

		$headers = array();
		$mimeType = $file->fileType->mime_type;
		if (!is_null($mimeType)) {
			// explicitly set the mime type
			// if not set it 'should' be detected automatically
			$headers["Content-Type"] = $mimeType;
		}

		// return response with cache header set for client to cache for a year
		return Response::download(Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $file->id, "la1tv-".$file->id, $headers)->setContentDisposition("inline")->setClientTtl(31556926)->setTtl(31556926)->setEtag($file->id);
	}
}