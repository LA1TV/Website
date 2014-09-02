<?php

return array(
	"appId"				=> $_ENV['FACEBOOK_APP_ID'],
	"appSecret"			=> $_ENV['FACEBOOK_APP_SECRET'],
	// interval in minutes that must pass before the system checks facebook for updated profile info
	"updateInterval"	=> 10
);
