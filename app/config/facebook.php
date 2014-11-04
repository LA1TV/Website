<?php

return array(
	"enabled"			=> true,
	"appId"				=> isset($_ENV['FACEBOOK_APP_ID']) ? $_ENV['FACEBOOK_APP_ID'] : null,
	"appSecret"			=> isset($_ENV['FACEBOOK_APP_SECRET']) ? $_ENV['FACEBOOK_APP_SECRET'] : null,
	// interval in minutes that must pass before the system checks facebook for updated profile info and whether token is still valid
	"updateInterval"	=> 10,
	"wantedPermissions"	=> array(
		array("id"=> "email", "description"=> "We would like this permission in order to send you emails to the email address you registered with facebook.")
	)
);
