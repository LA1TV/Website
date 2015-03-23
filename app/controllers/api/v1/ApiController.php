<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use uk\co\la1tv\website\api\ApiResponseDataGenerator;
use uk\co\la1tv\website\api\transformers\ShowTransformer;
use uk\co\la1tv\website\api\transformers\PlaylistTransformer;
use uk\co\la1tv\website\api\transformers\MediaItemTransformer;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;

class ApiController extends ApiBaseController {

	private $showTransformer = null;
	private $playlistTransformer = null;
	private $mediaItemTransformer = null;

	public function __construct(ShowTransformer $showTransformer, PlaylistTransformer $playlistTransformer, MediaItemTransformer $mediaItemTransformer, ApiResponseDataGenerator $apiResponseDataGenerator) {
		parent::__construct();
		$this->apiResponseDataGenerator = $apiResponseDataGenerator;
		
		// TODO remove rest
		$this->showTransformer = $showTransformer;
		$this->playlistTransformer = $playlistTransformer;
		$this->mediaItemTransformer = $mediaItemTransformer;
	}

	public function getService() {
		return $this->createResponseFromApiResponseData($this->apiResponseDataGenerator->generateServiceResponseData());
	}
	
	public function getShows() {
		return $this->createResponseFromApiResponseData($this->withCache("shows", 15, "generateShowsResponseData", []));
	}
	
	public function getShow($id) {
		return $this->createResponseFromApiResponseData($this->withCache("show-".$id, 15, "generateShowResponseData", [$id]));
	}
	
	public function getShowPlaylists($id) {
		return $this->createResponseFromApiResponseData($this->withCache("show-playlist-".$id, 15, "generateShowPlaylistsResponseData", [$id]));
	}
	
	public function getPlaylists() {
		return $this->createResponseFromApiResponseData($this->withCache("playlists", 15, "generatePlaylistsResponseData", []));
	}
	
	public function getPlaylist($id) {
		return $this->createResponseFromApiResponseData($this->withCache("playlist-".$id, 8, "generatePlaylistResponseData", [$id]));
	}
	
	public function getPlaylistMediaItems($id) {
		return $this->createResponseFromApiResponseData($this->withCache("playlist-media-items-".$id, 8, "generatePlaylistMediaItemsResponseData", [$id]));
	}
	
	public function getMediaItem($playlistId, $mediaItemId) {
		return $this->createResponseFromApiResponseData($this->withCache("media-item-".$playlistId."-".$mediaItemId, 5, "generateMediaItemResponseData", [$playlistId, $mediaItemId]));
	}
}
