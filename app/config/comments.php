<?php

return array(
	// the number of comments to retrieve at a time
	"number_to_retrieve"			=> 20,
	"number_allowed"				=> 3, // number of comments allowed for a user in the reset interval (below)
	"number_allowed_reset_interval"	=> 60, // seconds
	"station_name"					=> "LA1:TV",
	"station_profile_picture_uri"	=> asset("assets/img/station-profile-picture.png"),
	"get_base_uri"					=> URL::to("/") . "/player/comments",
	"post_base_uri"					=> URL::to("/") . "/player/post-comment",
	"delete_base_uri"				=> URL::to("/") . "/player/delete-comment",
);
