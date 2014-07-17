<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use Config;
use App;
use DB;
use Input;
use FormHelpers;
use Upload;
use Csrf;
use Auth;
use uk\co\la1tv\website\models\File;

class UploadController extends UploadBaseController {

	public function postIndex() {
		Auth::loggedInOr403();
		Upload::process();
		return Upload::getResponse();
	}
	
	// serve up a file
	// TODO: It think most of this logic should be moved into the upload service provider
	public function getIndex($id) {
		
		// TODO: this obviously needs changing but will do for now.
		Auth::loggedInOr403();
		
		// TODO: might need to eager load more relations in getIsAccessible
		$file = File::with("mediaItemWithBanner", "mediaItemWithCover", "playlistWithBanner", "playlistWithCover")->where("process_state", 1)->find($id);
		
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
			if (!is_null($file->mediaItemWithBanner) && $file->mediaItemWithBanner->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->mediaItemWithCover) && $file->mediaItemWithCover->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->playlistWithBanner) && $file->playlistWithBanner->getIsAccessible()) {
				$accessAllowed = true;
			}
			else if (!is_null($file->playlistWithCover) && $file->playlistWithCover->getIsAccessible()) {
				$accessAllowed = true;
			}
		}
		
		if (!$accessAllowed) {
			App::abort(403); // forbidden
			return;
		}
		
		return Response::download(Config::get("custom.files_location") . DIRECTORY_SEPARATOR . $file->id);		
	}
	
	// get process info about a file
	public function postProcessinfo() {
		
		Auth::loggedInOr403();
	
		$resp = array("success"=> false, "payload"=>null);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			// TODO: the info should only be returned if the file should be accessible to the public so a check is needed here
			$file = File::find($id);
			if (!is_null($file)) {
				$resp['payload'] = $file->getProcessInfo();
				$resp['success'] = true;
			}
		}
		return Response::json($resp);
	}
	
	// remove a temporary file
	public function postRemove() {
		
		Auth::loggedInOr403();
	
		$resp = array("success"=> false);
		if (Csrf::hasValidToken() && FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			$file = $this->getFile($id);
			if (!is_null($file)) {
				Upload::delete($file);
				$resp['success'] = true;
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
