<?php namespace uk\co\la1tv\website\controllers\home\guide;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use Config;
use Carbon;
use uk\co\la1tv\website\models\MediaItem;

class GuideController extends HomeBaseController {

	public function getIndex() {
		
		$dayOffset = 0;
		
		
		$startDate = Carbon::now()->startOfDay()->addDays($dayOffset);
		$endDate = Carbon::now()->startOfDay()->addDays($dayOffset + Config::get("liveGuide.daysPerPage"));
		
		$mediaItems = MediaItem::accessible()->scheduledPublishTimeBetweenDates($startDate, $endDate)->whereHas("liveStreamItem", function($q2) {
			$q2->accessible();
		})->get();
		
		dd($mediaItems->toArray());
	
		$this->setContent(View::make("home.guide.index"), "guide", "guide");
	}
}
