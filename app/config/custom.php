<?php

return array(
	"files_location"	=> storage_path() . DIRECTORY_SEPARATOR ."files",
	"file_chunks_location"	=> storage_path() . DIRECTORY_SEPARATOR ."file_chunks",
	"items_per_page"	=> 12,
	"base_url"			=> URL::to("/"),
	"admin_base_url"	=> URL::to("/") . "/admin",
	// the number of days an item can be considered active for
	"num_days_active"	=> 21,
	// items scheduled before the current date + this number of days will be considered active
	"num_days_future_before_active"	=> 7,
	// the time in minutes to cache certain query results. E.g the active shows and active playlists list.
	"cache_time"		=> 1,
	"live_stream_domains_cache_time"	=> 1,
	"popular_items_cache_time"	=> 10,
	"num_popular_items_to_cache"	=> 100,
	"num_recent_items"	=> 8,
	"num_popular_items"	=> 8,
	"num_playlists_per_page"	=> 20,
	"num_shows_per_page"	=> 20,
	"log_uri"			=> URL::to("/") . "/ajax/log", # where javascript log events should be posted to
	"js_log_file_path"	=> storage_path() . DIRECTORY_SEPARATOR ."logs" . DIRECTORY_SEPARATOR . "js-log.log",
	"blog_url"			=> "http://blog.la1tv.co.uk/",
	"default_cover_uri"		=> asset("assets/img/default-cover.jpg"),
	"open_graph_logo_uri"	=> asset("assets/img/og-logo.jpg"),
	"live_shows_uri"	=> URL::to("/") . "/player/live-shows",
	"player_info_base_uri"	=> URL::to("/") . "/player/player-info",
	"player_register_view_count_base_uri"	=> URL::to("/") . "/player/register-view",
	"player_register_like_base_uri"	=> URL::to("/") . "/player/register-like",
	"update_playback_time_base_uri"	=> URL::to("/") . "/player/register-playback-time",
	"player_base_uri"	=> URL::to("/") . "/player",
	"embed_default_cover_uri"	=> asset("assets/img/default-cover.png"),
	"embed_player_info_base_uri"	=> URL::to("/") . "/player/player-info",
	"embed_player_register_view_count_base_uri"	=> URL::to("/") . "/player/register-view",
	"embed_player_register_like_base_uri"	=> URL::to("/") . "/player/register-like",
	"embed_player_update_playback_time_base_uri"	=> URL::to("/") . "/player/register-playback-time",
	// time in minutes that must pass between views on the same item being registered
	"interval_between_registering_view_counts"	=> 5
);
