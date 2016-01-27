<?php

return array(
	// note if this is disabled push notifications also need disabling in the notifications service.
	"enabled"		=> isset($_ENV['PUSH_NOTIFICATIONS_ENABLED']) && $_ENV['PUSH_NOTIFICATIONS_ENABLED'],
	// this is used to send push notifications to google chrome browsers
	// if null push notifications won't work for chrome browsers
	"gcmApiKey"		=> isset($_ENV['GCM_API_KEY']) ? $_ENV['GCM_API_KEY'] : null,
	"gcmProjectNumber"	=> isset($_ENV['GCM_PROJECT_NUMBER']) ? $_ENV['GCM_PROJECT_NUMBER'] : "745046379475",
	// only endpoints which start with the following will be accepted
	"endpointWhiteList" => array(
		"https://android.googleapis.com/gcm/send/",
		"https://updates.push.services.mozilla.com/push/"
	)
);