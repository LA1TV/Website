<?php

class PlayerHelpers {
	
	public static function getInfoUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_info_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	public static function getRegisterWatchingUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_register_watching_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
	
	public static function getRegisterLikeUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_register_like_base_uri")."/".$playlistId ."/".$mediaItemId;
	}

	public static function getRecommendationsUri($playlistId, $mediaItemId) {
		return Config::get("custom.player_recommendations_base_uri")."/".$playlistId ."/".$mediaItemId;
	}
}