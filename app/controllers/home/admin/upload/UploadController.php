<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use Config;
use App;
use DB;
use Input;
use FormHelpers;
use Upload;
use uk\co\la1tv\website\models\File;

class UploadController extends UploadBaseController {

	public function postIndex() {
		Upload::process();
		return Upload::getResponse();
	}
	
	// serve up a file
	public function getIndex($id) {
		
		// TODO: might need to eager load more relations in getIsAccessible
		$file = File::with("mediaItemWithBanner", "mediaItemWithCover", "playlistWithBanner", "playlistWithCover")->find($id);
		
		if (is_null($file)) {
			App::abort(404);
			return;
		}
		
		$accessAllowed = false;
		
		// file should be accessible if not used yet and session matches users session
		if (!$file->in_use && $file->session_id === Session::getId()) {
			$accessAllowed = true;
		}
		else {
			// see if the file should be accessible
			if (!is_null($file->mediaItemWithBanner()->first()) && $file->mediaItemWithBanner()->first()->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->mediaItemWithCover()->first()) && $file->mediaItemWithCover()->first()->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->playlistWithBanner()->first()) && $file->playlistWithBanner()->first()->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->playlistWithCover()->first()) && $file->playlistWithCover()->first()->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		
		if (!$accessAllowed) {
			App::abort(403); // forbidden
			return;
		}
		
		return Response::download(Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $file->id);		
	}
	
	// get information about a temporary file
	public function postInfo() {
		$resp = array("success"=> false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
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
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
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
