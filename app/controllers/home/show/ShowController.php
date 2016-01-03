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
	
		$playlists = $show->playlists()->accessibleToPublic()->orderBy("series_no", "asc")->orderBy("name", "asc")->orderBy("description", "asc")->get();
		$showTableData = array();
		foreach($playlists as $i=>$item) {
			$thumbnailUri = $item->getCoverArtUri($coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
			$showTableData[] = array(
				"uri"					=> $item->getUri(),
				"title"					=> $item->generateName(),
				"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
				"playlistName"			=> null,
				"episodeNo"				=> null,
				"thumbnailUri"			=> $thumbnailUri,
				"thumbnailFooter"		=> null,
				"duration"				=> null,
				"active"				=> false
			);
		}
		
		$coverImageResolutions = Config::get("imageResolutions.coverImage");
		$coverUri = $show->getCoverUri($coverImageResolutions['full']['w'], $coverImageResolutions['full']['h']);
		$sideBannerImageResolutions = Config::get("imageResolutions.sideBannerImage");
		$sideBannerUri = $show->getSideBannerUri($sideBannerImageResolutions['full']['w'], $sideBannerImageResolutions['full']['h']);
		$sideBannerFillImageResolutions = Config::get("imageResolutions.sideBannerFillImage");
		$sideBannerFillUri = $show->getSideBannerFillUri($sideBannerFillImageResolutions['full']['w'], $sideBannerFillImageResolutions['full']['h']);
		$openGraphCoverArtUri = $show->getCoverArtUri($coverArtResolutions['fbOpenGraph']['w'], $coverArtResolutions['fbOpenGraph']['h']);
		$twitterCardCoverArtUri = $show->getCoverArtUri($coverArtResolutions['twitterCard']['w'], $coverArtResolutions['twitterCard']['h']);
		
		$twitterProperties = array();
		$twitterProperties[] = array("name"=> "card", "content"=> "summary_large_image");
		
		$openGraphProperties = array();
		if (!is_null($show->description)) {
			$openGraphProperties[] = array("name"=> "og:description", "content"=> $show->description);
			$twitterProperties[] = array("name"=> "description", "content"=> str_limit($show->description, 197, "..."));
		}
		$openGraphProperties[] = array("name"=> "video:release_date", "content"=> null);
		$twitterProperties[] = array("name"=> "title", "content"=> $show->name);
		$openGraphProperties[] = array("name"=> "og:title", "content"=> $show->name);
		$openGraphProperties[] = array("name"=> "og:image", "content"=> $openGraphCoverArtUri);
		$twitterProperties[] = array("name"=> "image", "content"=> $twitterCardCoverArtUri);
		foreach($showTableData as $a) {
			$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
		}
		
		$view = View::make("home.show.index");
		$view->showTitle = $show->name;
		$view->escapedShowDescription = !is_null($show->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($show->description)) : null;
		$view->coverImageUri = $coverUri;
		$view->showTableFragment = count($showTableData) > 0 ? View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> null,
			"tableData"		=> $showTableData
		)) : null;
		$this->setContent($view, "show", "show", $openGraphProperties, $show->name, 200, $twitterProperties, $sideBannerUri, $sideBannerFillUri);
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
