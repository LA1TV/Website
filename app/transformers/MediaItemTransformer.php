<?php namespace uk\co\la1tv\website\transformers;

use uk\co\la1tv\website\models\MediaItem;
use Config;

class MediaItemTransformer extends Transformer {
	
	// array where first element is the playlist, second is media item
	public function transform($mediaItemAndPlaylist) {
		if (count($mediaItemAndPlaylist) !== 2) {
			throw(new Exception("mediaItemAndPlaylist invalid."));
		}
		$playlist = $mediaItemAndPlaylist[0];
		$mediaItem = $mediaItemAndPlaylist[1];
		
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		$coverArtUris = [
			"thumbnail"		=> $playlist->getMediaItemCoverArtUri($mediaItem, $coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']),
			"full"			=> $playlist->getMediaItemCoverArtUri($mediaItem, $coverArtResolutions['full']['w'], $coverArtResolutions['full']['h']),
		];
		return [
			"id"				=> intval($mediaItem->id),
			"name"				=> $mediaItem->name,
			"description"		=> $mediaItem->description,
			"siteUri"			=> $playlist->getMediaItemUri($mediaItem),
			"coverArtUris"		=> $coverArtUris,
			"episodeNumber"		=> $playlist->getEpisodeNumber($mediaItem),
			"scheduledPublishTime"	=> $mediaItem->scheduled_publish_time->timestamp,
			"timeUpdated"		=> $mediaItem->updated_at->timestamp
		];
	}
	
}