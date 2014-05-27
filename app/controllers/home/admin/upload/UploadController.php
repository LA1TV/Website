<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;

class UploadController extends UploadBaseController {

	public function postIndex() {
		
		// A list of permitted file extensions
		$extensions = array('jpg', 'mp4');
		
		$resp = array("success"=> false);
		
		/*if (count($_FILES) >= 1) {
			$file = $_FILES[0];
			$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			if (in_array($extension, $extensions)) {
				// move it
				$resp['success'] = true;
				$resp['id'] = 0;
			}
		}
		*/
		$resp['success'] = true;
		$resp['id'] = 1;
		return Response::json($resp);
	}
}
