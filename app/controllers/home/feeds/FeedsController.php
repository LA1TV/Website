<?php namespace uk\co\la1tv\website\controllers\home\feeds;

use uk\co\la1tv\website\controllers\BaseController;
use Feed;
use Config;
use Carbon;
use URL;
use uk\co\la1tv\website\models\MediaItem;

class FeedsController extends BaseController {

	// provide an rss feed containing the latest items
	public function getLatest() {
		$feed = Feed::make();
		
		// cache response for a minute
		$feed->setCache(Config::get("custom.feeds_cache_time", 'feeds-latest'));
		
		if (!$feed->isCached()) {
			$items = MediaItem::getCachedRecentItems();
			$feed->title = "LA1:TV's Latest Content";
			$feed->description = 'This feed contains the latest content that is published to our website.';
			$feed->logo = asset("/assets/img/logo.png");
			$feed->link = URL::route('home');
			$feed->setDateFormat('datetime');
			$feed->pubdate = Carbon::now();
			$feed->lang = 'en';
			$feed->setShortening(false);
			foreach ($items as $item) {
				$mediaItem = $item['mediaItem'];
				$scheduledPublishTime = new Carbon($mediaItem->scheduled_publish_time);
				// title, author, url, pubdate, description
				$feed->add($item['generatedName']." [".$item['playlistName']."]", "LA1:TV", $item['uri'], $scheduledPublishTime, $mediaItem->description);
			}
		}
		return $feed->render('rss');
	}
}
