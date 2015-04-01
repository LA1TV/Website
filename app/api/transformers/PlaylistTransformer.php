<?php namespace uk\co\la1tv\website\api\transformers;

use uk\co\la1tv\website\models\Playlist;
use Config;

class PlaylistTransformer extends Transformer {
	
	public function transform($playlist, array $options) {
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		
		$showInfo = is_null($playlist->show_id) ? null : [
			"id"			=> intval($playlist->show_id),
			"seriesNumber"	=> intval($playlist->series_no)
		];
		$coverArtUris = [
			"thumbnail"		=> $playlist->getCoverArtUri($coverArtResolutions['thumbnail']['w'], $coverArtResolutions['thumbnail']['h']),
			"full"			=> $playlist->getCoverArtUri($coverArtResolutions['full']['w'], $coverArtResolutions['full']['h']),
		];
		return [
			"id"			=> intval($playlist->id),
			"name"			=> $playlist->name,
			"description"	=> $playlist->description,
			"show"			=> $showInfo,
			"siteUrl"		=> $playlist->getUri(),
			"coverArtUrls"	=> $coverArtUris,
			"timeUpdated"	=> $playlist->updated_at->timestamp
		];
	}
	
}