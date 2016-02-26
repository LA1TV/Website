<?php namespace uk\co\la1tv\website\controllers\home;

use View;
use uk\co\la1tv\website\models\MediaItem;
use Carbon;
use Auth;
use PlayerHelpers;
use Config;
use Cookie;
use Response;
use File;

class HomeController extends HomeBaseController {

	public function getIndex() {
	
		if (Config::get("degradedService.enabled")) {
			$view = View::make("home.degradedIndex");
			$view->contactEmail = Config::get("contactEmails.development");
			$this->setContent($view, "home", "home-degraded", array(), null, 200, array());
			return;
		}

		$promoMediaItem = MediaItem::with("liveStreamItem", "liveStreamItem.liveStream", "videoItem")->accessible()->whereNotNull("time_promoted")->orderBy("time_promoted", "desc")->first();
		if (!is_null($promoMediaItem)) {
			$liveStreamItem = $promoMediaItem->liveStreamItem;
			if (!is_null($liveStreamItem) && !$liveStreamItem->getIsAccessible()) {
				$liveStreamItem = null;
			}
			$videoItem = $promoMediaItem->videoItem;
			if (!is_null($videoItem) && !$videoItem->getIsAccessible()) {
				$videoItem = null;
			}

			$shouldShowItem = false;
			// if there is a live stream which is in the "not live" state the player won't display the vod
			// even if there is one. It will show the countdown to the start of the live stream.
			if (is_null($liveStreamItem) || !$liveStreamItem->isNotLive()) {
				if (!is_null($videoItem) && $videoItem->getIsLive()) {
					$shouldShowItem = true;
				}
				else if (!is_null($liveStreamItem) && $liveStreamItem->hasWatchableContent()) {
					$shouldShowItem = true;
				}
			}
			if (!$shouldShowItem) {
				$promoMediaItem = null;
			}
		}

		$promoPlaylist = null;
		if (!is_null($promoMediaItem)) {
			$promoPlaylist = $promoMediaItem->getDefaultPlaylist();
		}

		$promotedItems = MediaItem::getCachedPromotedItems();
		$promotedItemsData = array();

		// if there is an item to promote insert it at the start of the carousel
		if (!is_null($promoMediaItem)) {
			$coverArtResolutions = Config::get("imageResolutions.coverArt");
			$isLiveShow = !is_null($promoMediaItem->liveStreamItem) && !$promoMediaItem->liveStreamItem->isOver();
			$liveNow = $isLiveShow && $promoMediaItem->liveStreamItem->isLive();
			$promotedItemsData[] = array(
				"coverArtUri"	=> $promoPlaylist->getMediaItemCoverArtUri($promoMediaItem, $coverArtResolutions['full']['w'], $coverArtResolutions['full']['h']),
				"name"			=> $promoMediaItem->name,
				"seriesName"	=> !is_null($promoPlaylist->show) ? $promoPlaylist->generateName() : null,
				"availableMsg"	=> $liveNow ? "Live Now!" : $this->buildTimeStr($isLiveShow, $promoMediaItem->scheduled_publish_time),
				"uri"			=> $promoPlaylist->getMediaItemUri($promoMediaItem)
			);
		}
		
		foreach($promotedItems as $a) {
			$mediaItem = $a['mediaItem'];
			if (!is_null($promoMediaItem) && intval($mediaItem->id) === intval($promoMediaItem->id)) {
				// prevent duplicate
				continue;
			}
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
				"thumbnailFooter"		=> null,
				"duration"				=> $a['duration']
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
				"thumbnailFooter"		=> null,
				"duration"				=> $a['duration']
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
		$view->showFacebookWidget = Config::get("facebook.showTimelineWidget");
		$view->facebookPageUrl = Config::get("facebook.pageUrl");

		$hasPromoItem = !is_null($promoMediaItem);
		$showPromoItem = $hasPromoItem;
		if ($hasPromoItem) {
			// determine if the user has already seen the promo
			$cookieVal = Cookie::get('seenPromo-'.$promoMediaItem->id);
			if (!is_null($cookieVal) && $cookieVal === $promoMediaItem->time_promoted->timestamp) {
				// user already seen promo
				$showPromoItem = false;
			}
			// put a cookie in the users browser to inform us in the future that the user has seen this promo video
			// store the time so that if the item is repromoted in the future, it will be shown again.
			Cookie::queue('seenPromo-'.$promoMediaItem->id, $promoMediaItem->time_promoted->timestamp, 40320); // store for 4 weeks
		}

		$view->showPromoItem = $showPromoItem;
		if ($showPromoItem) {
			$userHasMediaItemsPermission = false;
			if (Auth::isLoggedIn()) {
				$userHasMediaItemsPermission = Auth::getUser()->hasPermission(Config::get("permissions.mediaItems"), 0);
			}
			$view->promoPlayerInfoUri = PlayerHelpers::getInfoUri($promoPlaylist->id, $promoMediaItem->id);
			$view->promoRegisterWatchingUri = PlayerHelpers::getRegisterWatchingUri($promoPlaylist->id, $promoMediaItem->id);
			$view->promoRegisterLikeUri = PlayerHelpers::getRegisterLikeUri($promoPlaylist->id, $promoMediaItem->id);
			$view->promoAdminOverrideEnabled = $userHasMediaItemsPermission;
			$view->promoLoginRequiredMsg = "Please log in to use this feature.";
		}

		$this->setContent($view, "home", "home", array(), null, 200, array());
	}
	
	public function getManifest() {
		$gcmSenderId = Config::get("pushNotifications.gcmApiKey");
		
		$data = array();
		if (Config::get("pushNotifications.enabled")) {
			// ensure it's a string
			$senderId = (string) Config::get("pushNotifications.gcmProjectNumber");
			if (!is_null($senderId)) {
				$data["gcm_sender_id"] = $senderId;
			}
		}
		return Response::json($data);
	}

	public function getServiceWorker() {
		// so that the service worker script will run everywhere
	 	$file = File::get(app_path()."/assets/service-workers/home.js");
		$response = Response::make($file, 200);
		$response->header('Content-Type', 'text/javascript');
		return $response;
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