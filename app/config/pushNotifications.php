<?php

return array(
	"enabled"		=> isset($_ENV['PUSH_NOTIFICATIONS_ENABLED']) && $_ENV['PUSH_NOTIFICATIONS_ENABLED'],
	// this is used to send push notifications to google chrome browsers
	// if null push notifications won't work for chrome browsers
	"gcmApiKey"		=> isset($_ENV['GCM_API_KEY']) ? $_ENV['GCM_API_KEY'] : null,
	"gcmProjectNumber"	=> isset($_ENV['GCM_PROJECT_NUMBER']) ? $_ENV['GCM_PROJECT_NUMBER'] : null
);
