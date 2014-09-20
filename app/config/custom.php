<?php

return array(
	"files_location"	=> storage_path() . DIRECTORY_SEPARATOR ."files",
	"pending_files_location"	=> storage_path() . DIRECTORY_SEPARATOR ."pending_files",
	"items_per_page"	=> 12,
	"base_url"			=> URL::to("/"),
	"admin_base_url"	=> URL::to("/") . "/admin",
	// the number of days an item can be considered active for
	"num_days_active"	=> 7,
	// items scheduled before the current date + this number of days will be considered active
	"num_days_future_before_active"	=> 7,
	// the time in minutes to cache certain query results. E.g the active shows and active playlists list.
	"cache_time"		=> 1,
	"blog_url"			=> "http://blog.la1tv.co.uk/",
	"default_cover_uri"	=> asset("assets/img/default-cover.png"),
	"player_info_base_uri"	=> URL::to("/") . "/player/player-info",
	"player_register_view_count_base_uri"	=> URL::to("/") . "/player/register-view",
	"player_register_like_base_uri"	=> URL::to("/") . "/player/register-like",
	"player_base_uri"	=> URL::to("/") . "/player",
	"embed_default_cover_uri"	=> asset("assets/img/default-cover.png"),
	"embed_player_info_base_uri"	=> URL::to("/") . "/player/player-info",
	"embed_player_register_view_count_base_uri"	=> URL::to("/") . "/player/register-view",
	"embed_player_register_like_base_uri"	=> URL::to("/") . "/player/register-like",
	// time in minutes that must pass between views on the same item being registered
	"interval_between_registering_view_counts"	=> 180
);
