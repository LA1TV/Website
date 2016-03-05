<?php
use uk\co\la1tv\website\models\PlaybackHistory;

class PlaylistTableHelpers {
	
	public static function getFooterObj($mediaItem) {
		if ($mediaItem->scheduled_publish_time->isPast()) {
			return null;
		}
		$mediaItem->load("liveStreamItem");
		$isLive = !is_null($mediaItem->liveStreamItem) && $mediaItem->liveStreamItem->getIsAccessible();
		if ($isLive && intval($mediaItem->liveStreamItem->getResolvedStateDefinition()->id) !== 1) {
			return null;
		}
		
		return array(
			"isLive"	=> $isLive,
			"dateTxt"	=> $mediaItem->scheduled_publish_time->format("d/m/y H:i")
		);
	}

	public static function getDuration($mediaItem) {
		$videoItem = $mediaItem->videoItem;
		if (is_null($videoItem) || !$videoItem->getIsLive()) {
			return null;
		}
		return $videoItem->getDurationPretty();
	}

	public static function getStatsObj($mediaItem) {
		$minNumberOfViews = Config::get("custom.min_number_of_views");
		$viewCount = PlaybackHistory::getViewCount(intval($mediaItem->id));
		if ($viewCount < $minNumberOfViews) {
			// too low to display
			$viewCount = null;
		}

		$numLikes = null;
		if ($mediaItem->likes_enabled) {
			$numLikes = $mediaItem->likes()->where("is_like", true)->count();
			if ($numLikes === 0) {
				$numLikes = null;
			}
		}

		return array(
			"viewCount"	=> $viewCount,
			"numLikes"	=> $numLikes
		);
	}
}