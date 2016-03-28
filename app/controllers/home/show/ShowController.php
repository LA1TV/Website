<?php namespace uk\co\la1tv\website\controllers\home\show;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use App;
use URLHelpers;
use Config;
use Cache;
use uk\co\la1tv\website\models\Show;

class ShowController extends HomeBaseController {

	public function getIndex($id=null) {
		if (is_null($id)) {
			App::abort(404);
		}

		$id = intval($id);
		$fromCache = Cache::remember("pages.show.".$id, 15, function() use (&$id) {
			$show = Show::with("playlists")->accessible()->find(intval($id));
			if (is_null($show)) {
				return null;
			}
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
			$playlists = $show->playlists()->accessibleToPublic()->orderBy("series_no", "asc")->orderBy("name", "asc")->orderBy("description", "asc")->get();
			$showTableData = array();
			foreach($playlists as $i=>$item) {
				$thumbnailUri = Config::get("custom.default_cover_uri");
				if (!Config::get("degradedService.enabled")) {
					$thumbnailUri = $item->getCoverArtUri($coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
				}
				$showTableData[] = array(
					"uri"					=> $item->getUri(),
					"title"					=> $item->generateName(),
					"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
					"playlistName"			=> null,
					"episodeNo"				=> null,
					"thumbnailUri"			=> $thumbnailUri,
					"thumbnailFooter"		=> null,
					"duration"				=> null,
					"active"				=> false,
					"stats"					=> null
				);
			}
			
			$coverUri = null;
			$sideBannerUri = null;
			$sideBannerFillUri = null;
			if (!Config::get("degradedService.enabled")) {
				$coverImageResolutions = Config::get("imageResolutions.coverImage");
				$coverUri = $show->getCoverUri($coverImageResolutions['full']['w'], $coverImageResolutions['full']['h']);
				$sideBannerImageResolutions = Config::get("imageResolutions.sideBannerImage");
				$sideBannerUri = $show->getSideBannerUri($sideBannerImageResolutions['full']['w'], $sideBannerImageResolutions['full']['h']);
				$sideBannerFillImageResolutions = Config::get("imageResolutions.sideBannerFillImage");
				$sideBannerFillUri = $show->getSideBannerFillUri($sideBannerFillImageResolutions['full']['w'], $sideBannerFillImageResolutions['full']['h']);
			}
			$openGraphCoverArtUri = $show->getCoverArtUri($coverArtResolutions['fbOpenGraph']['w'], $coverArtResolutions['fbOpenGraph']['h']);
			$twitterCardCoverArtUri = $show->getCoverArtUri($coverArtResolutions['twitterCard']['w'], $coverArtResolutions['twitterCard']['h']);
			
			$showName = $show->name;

			$twitterProperties = array();
			$twitterProperties[] = array("name"=> "card", "content"=> "summary_large_image");
			
			$openGraphProperties = array();
			if (!is_null($show->description)) {
				$openGraphProperties[] = array("name"=> "og:description", "content"=> $show->description);
				$twitterProperties[] = array("name"=> "description", "content"=> str_limit($show->description, 197, "..."));
			}
			$openGraphProperties[] = array("name"=> "video:release_date", "content"=> null);
			$twitterProperties[] = array("name"=> "title", "content"=> $showName);
			$openGraphProperties[] = array("name"=> "og:title", "content"=> $showName);
			$openGraphProperties[] = array("name"=> "og:image", "content"=> $openGraphCoverArtUri);
			$twitterProperties[] = array("name"=> "image", "content"=> $twitterCardCoverArtUri);
			foreach($showTableData as $a) {
				$openGraphProperties[] = array("name"=> "og:see_also", "content"=> $a['uri']);
			}
			$viewProps = array();
			$viewProps["showTitle"] = $showName;
			$viewProps["escapedShowDescription"] = !is_null($show->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($show->description)) : null;
			$viewProps["coverImageUri"] = $coverUri;
			$showTableFragmentData = count($showTableData) > 0 ? array(
				"stripedTable"	=> true,
				"headerRowData"	=> null,
				"tableData"		=> $showTableData
			) : null;

			return array(
				"viewProps"				=> $viewProps,
				"showTableFragmentData"	=> $showTableFragmentData,
				"showName"				=> $showName,
				"openGraphProperties"	=> $openGraphProperties,
				"twitterProperties"		=> $twitterProperties,
				"sideBannerUri"			=> $sideBannerUri,
				"sideBannerFillUri"		=> $sideBannerFillUri
			);
		}, true);
		
		if (is_null($fromCache)) {
			App::abort(404);
			return;
		}

		$cachedViewProps = $fromCache["viewProps"];
		$showTableFragmentData = $fromCache["showTableFragmentData"];
		$showName = $fromCache["showName"];
		$openGraphProperties = $fromCache["openGraphProperties"];
		$twitterProperties = $fromCache["twitterProperties"];
		$sideBannerUri = $fromCache["sideBannerUri"];
		$sideBannerFillUri = $fromCache["sideBannerFillUri"];

		$view = View::make("home.show.index");
		foreach($cachedViewProps as $b=>$a) {
			$view[$b] = $a;
		}

		$view->showTableFragment = !is_null($showTableFragmentData) ? View::make("fragments.home.playlist", $showTableFragmentData) : null;
		$this->setContent($view, "show", "show", $openGraphProperties, $showName, 200, $twitterProperties, $sideBannerUri, $sideBannerFillUri);
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
