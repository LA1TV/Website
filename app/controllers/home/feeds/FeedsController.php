<?php namespace uk\co\la1tv\website\controllers\home\feeds;

use uk\co\la1tv\website\controllers\BaseController;
use Feed;
use Config;
use uk\co\la1tv\website\models\MediaItem;

class FeedsController extends BaseController {

	// provide an rss feed containing the latest items
	public function getLatest() {
		$feed = Feed::make();
		
		// cache response for a minute
		$feed->setCache(Config::get("custom.feeds_cache_time", 'feeds-latest');
	
		if (!$feed->isCached()) {
			$items = MediaItem::getCachedRecentItems();
			$feed->title = "LA1:TV's Latest Content";
			$feed->description = 'This feed contains the latest content that is published to our website.';
			$feed->logo = asset("/assets/img/logo.png");
			$feed->link = URL::to('feeds-latest'); // TODO maybe this should be to the homepage?
			$feed->setDateFormat('carbon');
			$feed->pubdate = TODO; // TODO
			$feed->lang = 'en';
			$feed->setShortening(false);
			foreach ($posts as $post) {
				// set item's title, author, url, pubdate, description and content
				// TODO
				//$feed->add($post->title, $post->author, URL::to($post->slug), $post->created, $post->description, $post->content);
			}

	}
	return $feed->render('rss');
}
