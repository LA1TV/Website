<?php namespace uk\co\la1tv\website\controllers\home;

use View;
use uk\co\la1tv\website\models\MediaItem;
use Carbon;

class HomeController extends HomeBaseController {

	public function getIndex() {
	
		$promotedItems = MediaItem::getCachedPromotedItems();
		$promotedItemsData = array();
		
		foreach($promotedItems as $a) {
			$mediaItem = $a['mediaItem'];
			$isLiveShow = !is_null($mediaItem->liveStreamItem);
			$liveNow = $isLiveShow && $mediaItem->liveStreamItem->isLive();
			$promotedItemsData[] = array(
				"coverArtUri"	=> $a['coverArtUri'],
				"name"			=> $a['generatedName'],
				"availableMsg"	=> $liveNow ? "Live Now!" : $this->buildTimeStr($isLiveShow, $mediaItem->scheduled_publish_time),
				"uri"			=> $a['uri']
			);
		}
		
		
		$view = View::make("home.index");
		
		$view->promotedItemsData = $promotedItemsData;
		$this->setContent($view, "home", "home");
	}
	
	private function buildTimeStr($isLive, $time) {
		
		$liveStr = $isLive ? "Live" : "Available";
		
		if ($time->isPast()) {
			return "Available On Demand";
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
