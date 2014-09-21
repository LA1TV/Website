<?php namespace uk\co\la1tv\website\controllers\home\show;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use URLHelpers;
use Config;
use uk\co\la1tv\website\models\Show;

class ShowController extends HomeBaseController {

	public function getIndex($id) {
		
		$show = Show::with("playlists")->accessible()->find(intval($id));
		if (is_null($show)) {
			App::abort(404);
		}
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
	
		$showTableData = array();
		foreach($show->playlists as $i=>$item) {
			$thumbnailUri = $item->getCoverArtUri($coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
			$showTableData[] = array(
				"uri"					=> $item->getUri(),
				"title"					=> $item->generateName(),
				"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
				"playlistName"			=> null,
				"episodeNo"				=> null,
				"thumbnailUri"			=> $thumbnailUri,
				"active"				=> false
			);
		}
	
		$view = View::make("home.show.index");
		$view->showTitle = $show->name;
		$view->escapedShowDescription = !is_null($show->description) ? URLHelpers::escapeAndReplaceUrls($show->description) : null;
		$view->coverImageUri = null; // TODO
		$view->showTableFragment = count($showTableData) > 0 ? View::make("fragments.home.playlist", array(
			"headerRowData"	=> null,
			"tableData"		=> $showTableData
		)) : null;
		$this->setContent($view, "show", "show");
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
