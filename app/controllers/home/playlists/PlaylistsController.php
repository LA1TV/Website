<?php namespace uk\co\la1tv\website\controllers\home\playlists;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use Config;
use Carbon;
use URL;
use App;
use uk\co\la1tv\website\models\Playlist;

class PlaylistsController extends HomeBaseController {

	public function getIndex($pageNo=0) {
		$pageNo = intval($pageNo);
		
		$itemsPerPage = intval(Config::get("custom.num_playlists_per_page"));
		
		$itemOffset = $pageNo*$itemsPerPage;
		
		$numPlaylists = Playlist::accessible()->belongsToShow(false)->count();
		$numPages = ceil($numPlaylists/$itemsPerPage);
		$playlists = Playlist::accessible()->belongsToShow(false)->orderBy("name", "asc")->orderBy("description", "asc")->skip($itemOffset)->take($itemsPerPage)->get();
		
		if ($pageNo > 0 && $playlists->count() === 0) {
			App::abort(404);
		}
		
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		$playlistTableData = array();
		
		foreach($playlists as $i=>$item) {
			$thumbnailUri = $item->getCoverArtUri($coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
			
			$playlistTableData[] = array(
				"uri"					=> $item->getUri(),
				"title"					=> $item->generateName(),
				"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
				"playlistName"			=> null,
				"episodeNo"				=> null,
				"thumbnailUri"			=> $thumbnailUri,
				"thumbnailFooter"		=> null,
				"active"				=> false
			);
		}
		
		$playlistFragment = count($playlistTableData) > 0 ? View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> null,
			"tableData"		=> $playlistTableData
		)) : null;
		
		$pageNumbers = array();
		for ($i=0; $i<$numPages; $i++) {
			$pageNumbers[] = array(
				"num"		=> $i+1,
				"uri"		=> URL::route("playlists", array($i)),
				"active"	=> $i === $pageNo
			);
		}
		
		$openGraphProperties = array();
		$openGraphProperties[] = array("name"=> "video:release_date", "content"=> null);
		foreach($playlistTableData as $a) {
			$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
		}
		
		$view = View::make("home.playlists.index");
		$view->playlistFragment = $playlistFragment;
		$view->pageSelectorFragment = View::make("fragments.home.pageSelector", array(
			"nextUri" 	=> $pageNo < $numPages-1 ? URL::route("playlists", array($pageNo+1)) : null,
			"prevUri"	=> $pageNo > 0 ? URL::route("playlists", array($pageNo-1)) : null,
			"numbers"	=> $pageNumbers
		));
		$this->setContent($view, "playlists", "playlists", $openGraphProperties);
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
