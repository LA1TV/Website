<?php namespace uk\co\la1tv\website\controllers\home;

use App;
use Redirect;
use uk\co\la1tv\website\controllers\BaseController;
use uk\co\la1tv\website\models\CustomUri;

class SlugController extends BaseController {

	public function getIndex($slug) {
		$customUri = CustomUri::where("name", $slug)->first();
		if (is_null($customUri)) {
			App::abort(404);
		}
		$uriable = $customUri->uriable;
		$uri = null;
		if (get_class($uriable) === "uk\co\la1tv\website\models\Playlist") {
			if ($uriable->getIsAccessible()) {
				$uri = $uriable->getUri();
			}
		}
		
		if (is_null($uri)) {
			App::abort(404);
		}
		else {
			return Redirect::to($uri);
		}
	}
}
