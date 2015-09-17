<?php namespace uk\co\la1tv\website\controllers\home;

use View;
use uk\co\la1tv\website\models\MediaItem;
use Carbon;
use Config;

class HomeController extends HomeBaseController {

	public function getIndex() {
	
		$promotedItems = MediaItem::getCachedPromotedItems();
		$promotedItemsData = array();
		
		foreach($promotedItems as $a) {
			$mediaItem = $a['mediaItem'];
			$isLiveShow = !is_null($mediaItem->liveStreamItem) && !$mediaItem->liveStreamItem->isOver();
			$liveNow = $isLiveShow && $mediaItem->liveStreamItem->isLive();
			$promotedItemsData[] = array(
				"coverArtUri"	=> $a['coverArtUri'],
				"name"			=> $mediaItem->name,
				"seriesName"	=> $a['seriesName'],
				"availableMsg"	=> $liveNow ? "Live Now!" : $this->buildTimeStr($isLiveShow, $mediaItem->scheduled_publish_time),
				"uri"			=> $a['uri']
			);
		}
		
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		$recentlyAddedItems = MediaItem::getCachedRecentItems();
		$recentlyAddedTableData = array();
		foreach($recentlyAddedItems as $i=>$a) {
			$mediaItem = $a['mediaItem'];
			$recentlyAddedTableData[] = array(
				"uri"					=> $a['uri'],
				"active"				=> false,
				"title"					=> $mediaItem->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $a['playlistName'],
				"episodeNo"				=> $i+1,
				"thumbnailUri"			=> $a['coverArtUri'],
				"thumbnailFooter"		=> null
			);
		}
		
		$mostPopularItems = MediaItem::getCachedMostPopularItems();
		$mostPopularTableData = array();
		foreach($mostPopularItems as $i=>$a) {
			$mediaItem = $a['mediaItem'];
			$mostPopularTableData[] = array(
				"uri"					=> $a['uri'],
				"active"				=> false,
				"title"					=> $mediaItem->name,
				"escapedDescription"	=> null,
				"playlistName"			=> $a['playlistName'],
				"episodeNo"				=> $i+1,
				"thumbnailUri"			=> $a['coverArtUri'],
				"thumbnailFooter"		=> null
			);
		}
		
		$view = View::make("home.index");
		
		$view->promotedItemsData = $promotedItemsData;
		$view->recentlyAddedPlaylistFragment = count($recentlyAddedTableData) > 0 ? View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> null,
			"tableData"		=> $recentlyAddedTableData
		)) : null;
		$view->mostPopularPlaylistFragment = count($mostPopularTableData) > 0 ? View::make("fragments.home.playlist", array(
			"stripedTable"	=> true,
			"headerRowData"	=> null,
			"tableData"		=> $mostPopularTableData
		)) : null;
		$view->twitterWidgetId = Config::get("twitter.timeline_widget_id");
		$this->setContent($view, "home", "home", array(), null, 200, array());
	}
	
	private function buildTimeStr($isLive, $time) {
		$liveStr = $isLive ? "Live" : "Available";
		
		if ($time->isPast()) {
			if (!$isLive) {
				return "Available On Demand Now";
			}
			else {
				return "Live Shortly";
			}
		}
		else if ($time->isToday()) {
			return $liveStr." Today at ".$time->format("H:i");
		}
		else if ($time->isTomorrow()) {
			return $liveStr." Tomorrow at ".$time->format("H:i");
		}
		else if (Carbon::now()->addYears(1)->timestamp <= $time->timestamp) {
			return "Coming Soon";
		}
		return $liveStr." at ".$time->format("H:i")." on ".$time->format("jS F");
	}
}
