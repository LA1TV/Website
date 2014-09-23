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
		})->orderBy("scheduled_publish_time", "asc")->orderBy("name", "asc")->orderBy("description", "asc")->get();
		
		// of form ("dateStr", "mediaItems")
		$calendarData = array();
		$previousMediaItem = null;
		
		foreach($mediaItems as $a) {
			if (is_null($previousMediaItem) || $previousMediaItem->scheduled_publish_time->startOfDay()->timestamp !== $a->scheduled_publish_time->startOfDay()->timestamp) {
				// new day
				$calendarData[] = array(
					"dateStr"		=> $a->scheduled_publish_time->startOfDay()->format('dS M'),
					"mediaItems"	=> array()
				);
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
					// TODO: figure out how to get episode no
					"episodeNo"				=> null,
					"thumbnailUri"			=> $thumbnailUri,
					"active"				=> false
				);
			}
			
			
			
		
			$playlistFragment = View::make("fragments.home.playlist", array(
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
		$view->startDateStr = $startDate->format('dS M');
		$view->endDateStr = $endDate->format('dS M');
		$this->setContent($view, "guide", "guide");
	}
}
