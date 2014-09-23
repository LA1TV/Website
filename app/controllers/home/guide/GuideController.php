<?php namespace uk\co\la1tv\website\controllers\home\guide;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use Config;
use Carbon;
use URL;
use App;
use uk\co\la1tv\website\models\MediaItem;

class GuideController extends HomeBaseController {

	public function getIndex($dayGroupOffset=0) {
		$dayGroupOffset = intval($dayGroupOffset);
		
		$daysPerPage = intval(Config::get("liveGuide.daysPerPage"));
		$numPages = intval(Config::get("liveGuide.numPages"));
		
		if (abs($dayGroupOffset) > $numPages) {
			App::abort(404);
		}
		
		$dayOffset = $dayGroupOffset*$daysPerPage;
		
		$startDate = Carbon::now()->startOfDay()->addDays($dayOffset);
		$endDate = Carbon::now()->startOfDay()->addDays($dayOffset + $daysPerPage);
		
		$mediaItems = MediaItem::accessible()->scheduledPublishTimeBetweenDates($startDate, $endDate)->whereHas("liveStreamItem", function($q2) {
			$q2->accessible();
		})->orderBy("scheduled_publish_time", "asc")->orderBy("name", "asc")->orderBy("description", "asc")->get();
		
		// of form ("dateStr", "mediaItems")
		$calendarData = array();
		$previousMediaItem = null;
		$lastDate = $startDate;
		foreach($mediaItems as $a) {
			if (is_null($previousMediaItem) || $previousMediaItem->scheduled_publish_time->startOfDay()->timestamp !== $a->scheduled_publish_time->startOfDay()->timestamp) {
				// new day
				$calendarData[] = array(
					"dateStr"		=> $this->getDateString($a->scheduled_publish_time->startOfDay()),
					"mediaItems"	=> array()
				);
				$lastDate = $a->scheduled_publish_time->startOfDay();
			}
			$calendarData[count($calendarData)-1]['mediaItems'][] = $a;
			$previousMediaItem = $a;
		}
		
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		$viewCalendarData = array();
		foreach($calendarData as $day) {
			$playlistTableData = array();
			
			foreach($day['mediaItems'] as $i=>$item) {
				$playlist = $item->getDefaultPlaylist();
				$thumbnailUri = $playlist->getMediaItemCoverArtUri($item, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']);
				$playlistName = null;
				if (!is_null($playlist->show)) {
					// the current item in the playlist is part of a show.
					$playlistName = $playlist->generateName();
				}
				$playlistTableData[] = array(
					"uri"					=> $playlist->getMediaItemUri($item),
					"title"					=> $playlist->generateEpisodeTitle($item),
					"escapedDescription"	=> !is_null($item->description) ? e($item->description) : null,
					"playlistName"			=> $playlistName,
					"episodeNo"				=> null,
					"thumbnailUri"			=> $thumbnailUri,
					"thumbnailFooter"		=> array(
						"isLive"	=> true,
						"dateTxt"	=> $item->scheduled_publish_time->format("H:i")
					),
					"active"				=> false
				);
			}
			
			$playlistFragment = View::make("fragments.home.playlist", array(
				"stripedTable"	=> false,
				"headerRowData"	=> null,
				"tableData"		=> $playlistTableData
			));
			
			$viewCalendarData[] = array(
				"dateStr"			=> $day['dateStr'],
				"playlistFragment"	=> $playlistFragment
			);
		}
		
		$view = View::make("home.guide.index");
		$view->calendarData = $viewCalendarData;
		$view->titleDatesStr = $this->getDateString($startDate) . " - " . $this->getDateString((new Carbon($endDate))->subDays(1));
		$view->previousPageUri = $dayGroupOffset !== -1*$numPages ? URL::route("guide", array($dayGroupOffset-1)) : null;
		$view->previousPageStartDateStr = $this->getDateString($startDate->subDays(1));
		$view->nextPageUri = $dayGroupOffset !== $numPages ? URL::route("guide", array($dayGroupOffset+1)) : null;
		$view->nextPageStartDateStr = $this->getDateString($endDate);
		$this->setContent($view, "guide", "guide");
	}
	
	private function getDateString($date) {
		if ($date->year !== Carbon::now()->year) {
			return $date->format('jS M y');
		}
		else {
			return $date->format('jS M');
		}
	}
	
	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && preg_match("/^\-?[0-9]+$/", $parameters[0])) {
			return call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			return parent::missingMethod($parameters);
		}
	}
}
