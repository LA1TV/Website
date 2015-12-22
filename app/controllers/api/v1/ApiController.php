<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use uk\co\la1tv\website\api\ApiResponseDataGenerator;
use ApiAuth;
use FormHelpers;

class ApiController extends ApiBaseController {

	private $apiResponseDataGenerator = null;

	public function __construct(ApiResponseDataGenerator $apiResponseDataGenerator) {
		parent::__construct();
		$this->apiResponseDataGenerator = $apiResponseDataGenerator;
	}

	public function getService() {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for service info.");
		return $this->createResponseFromApiResponseData($this->apiResponseDataGenerator->generateServiceResponseData());
	}
	
	public function getPermissions() {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for permissions.");
		return $this->createResponseFromApiResponseData($this->apiResponseDataGenerator->generatePermissionsResponseData(ApiAuth::getUser()->canViewVodUris(), ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canUseWebhooks()));
	}
	
	public function getShows() {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for shows.");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generateShowsResponseData", []));
	}
	
	public function getShow($id) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for show with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generateShowResponseData", [$id]));
	}
	
	public function getShowPlaylists($id) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for playlists for show with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generateShowPlaylistsResponseData", [$id]));
	}
	
	public function getPlaylists() {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for playlists.");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generatePlaylistsResponseData", []));
	}
	
	public function getPlaylist($id) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for playlist with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache(8, "generatePlaylistResponseData", [$id, ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canViewVodUris()]));
	}
	
	public function getPlaylistMediaItems($id) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for media items for playlist with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache(8, "generatePlaylistMediaItemsResponseData", [$id, ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canViewVodUris()]));
	}
	
	public function getPlaylistMediaItem($playlistId, $mediaItemId) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for playlist media item with id ".$mediaItemId." in playlist with id ".$playlistId.".");
		return $this->createResponseFromApiResponseData($this->withCache(5, "generatePlaylistMediaItemResponseData", [$playlistId, $mediaItemId, ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canViewVodUris()]));
	}

	public function getMediaItems() {
		ApiAuth::hasUserOrApiException();
		$limit = intval(FormHelpers::getValue("limit", "50", false, true));
		$sortMode = FormHelpers::getValue("sortMode", "SCHEDULED_PUBLISH_TIME", false, true);
		$sortDirection = FormHelpers::getValue("sortDirection", "DESC", false, true);
		$vodIncludeSetting = FormHelpers::getValue("vodIncludeSetting", "VOD_OPTIONAL", false, true);
		$streamIncludeSetting = FormHelpers::getValue("streamIncludeSetting", "STREAM_OPTIONAL", false, true);
		
		if (
			$limit < 1 ||
			($sortMode !== "SCHEDULED_PUBLISH_TIME" && $sortMode !== "POPULARITY") ||
			($sortDirection !== "ASC" && $sortDirection !== "DESC") ||
			($sortDirection === "ASC" && $sortMode === "POPULARITY") ||
			!in_array($vodIncludeSetting, ["VOD_OPTIONAL", "HAS_VOD", "HAS_AVAILABLE_VOD"], true) ||
			!in_array($streamIncludeSetting, ["STREAM_OPTIONAL", "HAS_STREAM", "HAS_LIVE_STREAM"], true)
		){
			return $this->respondServerError("Something is wrong with the provided query parameters.");
		}
	
		$this->log("Request for media items.");
		return $this->createResponseFromApiResponseData($this->withCache(60, "generateMediaItemsResponseData", [$limit, $sortMode, $sortDirection, $vodIncludeSetting, $streamIncludeSetting, ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canViewVodUris()]));
	}
	
	public function getMediaItem($mediaItemId) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for media item with id ".$mediaItemId.".");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generateMediaItemResponseData", [$mediaItemId, ApiAuth::getUser()->canViewStreamUris(), ApiAuth::getUser()->canViewVodUris()]));
	}
	
	public function getMediaItemPlaylists($mediaItemId) {
		ApiAuth::hasUserOrApiException();
		$this->log("Request for media item playlists with id ".$mediaItemId.".");
		return $this->createResponseFromApiResponseData($this->withCache(15, "generateMediaItemPlaylistsResponseData", [$mediaItemId]));
	}
}
