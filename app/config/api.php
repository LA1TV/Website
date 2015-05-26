<?php

return array(
	// if false then the api will return a service unavailable response.
	"enabled"		=> isset($_ENV['API_ENABLED']) && $_ENV['API_ENABLED'],
	// the maximum amount of items that can be retrieved from the /mediaItems endpoint
	"mediaItemsMaxRetrieveLimit"	=> 100
);
