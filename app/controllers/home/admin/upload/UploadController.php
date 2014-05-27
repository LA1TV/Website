<?php namespace uk\co\la1tv\website\controllers\home\admin\upload;

use Response;

class UploadController extends UploadBaseController {

	public function postIndex() {
		
		// A list of permitted file extensions
		$extensions = array('jpg', 'mp4');
		
		$resp = array("success"=> false);
		
		if (isset($_FILES['files']) && count($_FILES['files']['name']) >= 1) {
			
			$extension = strtolower(pathinfo($_FILES['files']['name'][0], PATHINFO_EXTENSION));
			if (in_array($extension, $extensions)) {
				// TODO: move the file
				$resp['success'] = true;
				$resp['id'] = 0;
			}
		}
		
		return Response::json($resp);
	}
}
