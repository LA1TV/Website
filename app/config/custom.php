<?php

return array(
	"files_location"	=> storage_path() . DIRECTORY_SEPARATOR ."files",
	"pending_files_location"	=> storage_path() . DIRECTORY_SEPARATOR ."pending_files",
	"items_per_page"	=> 12,
	"base_url"			=> URL::to("/"),
	"admin_base_url"	=> URL::to("/") . "/admin",
	// the number of days an item can be considered active for
	"num_days_active"	=> 7,
	// the time in minutes to cache certain query results. E.g the active shows and active playlists list.
	"cache_time"		=> 1,
	"blog_url"			=> "http://blog.la1tv.co.uk/",
	"default_cover_uri"	=> asset("assets/img/default-cover.png"),
	"player_info_base_uri"	=> URL::to("/") . "/player/player-info"
);
