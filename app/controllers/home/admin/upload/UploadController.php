<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;
use Session;
use Config;
use App;
use DB;
use Input;
use FormHelpers;
use Upload;
use Auth;
use uk\co\la1tv\website\models\File;

class UploadController extends UploadBaseController {

	public function postIndex() {
		Auth::loggedInOr403();
		if (Config::get("degradedService.enabled")) {
			return App::abort(503); // service unavailable 
		}
		Upload::process();
		return Upload::getResponse();
	}
	
	// serve up a file (or redirect to file)
	public function getIndex($id) {
		if (Config::get("degradedService.enabled")) {
			return App::abort(503); // service unavailable 
		}
		return Upload::getFileResponse($id);
	}
	
	// get process info about a file
	// should only be available to a user logged into the cms
	public function postProcessinfo() {
		
		Auth::loggedInOr403();
	
		$resp = array("success"=> false, "payload"=>null);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
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

		if (Config::get("degradedService.enabled")) {
			return App::abort(503); // service unavailable 
		}
	
		$resp = array("success"=> false);
		if (FormHelpers::hasPost("id")) {
			$id = intval($_POST["id"], 10);
			$file = File::find($id);
			if (!is_null($file) && $file->isTemporaryFromCurrentSession()) {
				Upload::delete($file);
				$resp['success'] = true;
			}
		}
		return Response::json($resp);
	}
	
	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && ctype_digit($parameters[0])) {
			return call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			return parent::missingMethod($parameters);
		}
	}
}
