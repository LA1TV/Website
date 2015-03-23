<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use uk\co\la1tv\website\api\ApiResponseDataGenerator;

class ApiController extends ApiBaseController {

	private $apiResponseDataGenerator = null;

	public function __construct(ApiResponseDataGenerator $apiResponseDataGenerator) {
		parent::__construct();
		$this->apiResponseDataGenerator = $apiResponseDataGenerator;
	}

	public function getService() {
		$this->log("Request for service info.");
		return $this->createResponseFromApiResponseData($this->apiResponseDataGenerator->generateServiceResponseData());
	}
	
	public function getShows() {
		$this->log("Request for shows.");
		return $this->createResponseFromApiResponseData($this->withCache("shows", 15, "generateShowsResponseData", []));
	}
	
	public function getShow($id) {
		$this->log("Request for show with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache("show-".$id, 15, "generateShowResponseData", [$id]));
	}
	
	public function getShowPlaylists($id) {
		$this->log("Request for playlists for show with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache("show-playlist-".$id, 15, "generateShowPlaylistsResponseData", [$id]));
	}
	
	public function getPlaylists() {
		$this->log("Request for playlists.");
		return $this->createResponseFromApiResponseData($this->withCache("playlists", 15, "generatePlaylistsResponseData", []));
	}
	
	public function getPlaylist($id) {
		$this->log("Request for playlist with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache("playlist-".$id, 8, "generatePlaylistResponseData", [$id]));
	}
	
	public function getPlaylistMediaItems($id) {
		$this->log("Request for media items for playlist with id ".$id.".");
		return $this->createResponseFromApiResponseData($this->withCache("playlist-media-items-".$id, 8, "generatePlaylistMediaItemsResponseData", [$id]));
	}
	
	public function getMediaItem($playlistId, $mediaItemId) {
		$this->log("Request for media item with id ".$mediaItemId." in playlist with id ".$playlistId.".");
		return $this->createResponseFromApiResponseData($this->withCache("media-item-".$playlistId."-".$mediaItemId, 5, "generateMediaItemResponseData", [$playlistId, $mediaItemId]));
	}
}
