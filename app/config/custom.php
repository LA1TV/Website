<?php

return array(
	"site_description"	=> "Lancaster University's Student Union TV station.",
	"home_redirect_url"	=> isset($_ENV['HOME_REDIRECT_URL']) ? $_ENV['HOME_REDIRECT_URL'] : null,
	"files_location"	=> isset($_ENV['FILES_LOCATION']) ? $_ENV['FILES_LOCATION'] : storage_path() . DIRECTORY_SEPARATOR ."files",
	"file_chunks_location"	=> isset($_ENV['FILE_CHUNKS_LOCATION']) ? $_ENV['FILE_CHUNKS_LOCATION'] : storage_path() . DIRECTORY_SEPARATOR ."file_chunks",
	"items_per_page"	=> 12,
	"base_url"			=> URL::to("/"),
	"admin_base_url"	=> URL::to("/") . "/admin",
	// the number of days an item can be considered active for
	"num_days_active"	=> 21,
	// items scheduled before the current date + this number of days will be considered active
	"num_days_future_before_active"	=> 7,
	// the time in minutes to cache certain query results. E.g the active shows and active playlists list.
	"cache_time"		=> 1,
	"feeds_cache_time"	=> 1,
	"live_stream_domains_cache_time"	=> 1,
	"num_popular_items_to_cache"	=> 100,
	"popular_items_weight_period"	=> 30,
	"popular_items_weight"			=> 25,
	"num_recent_items"	=> 15,
	// time to cache reent items in minutes.
	"recent_items_cache_time"	=> 2,
	"num_popular_items"	=> 15,
	// time to cache popular items in minutes.
	"popular_items_cache_time"	=> 10,
	"num_playlists_per_page"	=> 20,
	"num_shows_per_page"	=> 20,
	"min_number_of_views"	=> 100, // view counts will only be sent to the client when the sum of live stream + vod goes >= this number (unless logged in with media item view permission)
	"min_num_watching_now"	=> 3, // number of people watching now will only be sent to the client when it is higher than this number (unless logged in with media item view permission)
	"log_uri"			=> URL::to("/") . "/ajax/log", # where javascript log events should be posted to
	"js_log_file_path"	=> storage_path() . DIRECTORY_SEPARATOR ."logs" . DIRECTORY_SEPARATOR . "js-log.log",
	"blog_url"			=> isset($_ENV['BLOG_URL']) ? $_ENV['BLOG_URL'] : null,
	"default_cover_uri"		=> asset("assets/img/default-cover.jpg"),
	"open_graph_logo_uri"	=> asset("assets/img/og-logo.jpg"),
	"twitter_card_logo_uri"	=> asset("assets/img/og-logo.jpg"),
	"live_shows_uri"	=> URL::to("/") . "/player/live-shows",
	"player_info_base_uri"	=> URL::to("/") . "/player/player-info",
	"playlist_info_base_uri"	=> URL::to("/") . "/playlist/playlist-info",
	"player_register_watching_base_uri"	=> URL::to("/") . "/player/register-watching",
	"player_register_like_base_uri"	=> URL::to("/") . "/player/register-like",
	"player_base_uri"	=> URL::to("/") . "/player",
	"search_query_uri"	=> URL::to("/") . "/ajax/search",
	"embed_default_cover_uri"	=> asset("assets/img/default-cover.png"),
	"embed_player_info_base_uri"	=> URL::to("/") . "/mediaitem/player-info",
	"embed_player_register_watching_base_uri"	=> URL::to("/") . "/mediaitem/register-watching",
	"embed_player_register_like_base_uri"	=> URL::to("/") . "/mediaitem/register-like",
	"live_stream_player_info_base_uri"	=> URL::to("/") . "/livestream/player-info",
	"live_stream_player_register_watching_base_uri"	=> URL::to("/") . "/livestream/register-watching",
	"live_stream_player_schedule_base_uri"	=> URL::to("/") . "/livestream/schedule-info",
	"embed_live_stream_player_info_base_uri"	=> URL::to("/") . "/livestream/player-info",
	"embed_live_stream_player_register_watching_base_uri"	=> URL::to("/") . "/livestream/register-watching",
	// time in minutes that must pass between views on the same item being registered
	"interval_between_registering_view_counts"	=> 5
);
