<?php

return array(
	// the url to the service running https://github.com/tjenkinson/hls-live-thumbnails
	// live thumbnails disabled if null
	"serviceBaseUri"	=> isset($_ENV['LIVE_THUMBNAILS_SERVICE_BASE_URI']) ? $_ENV['LIVE_THUMBNAILS_SERVICE_BASE_URI'] : null,
	// the base url that the thumbnails will be served at e.g. http://example.com/thumbnails
	"publicBaseUri"	=> isset($_ENV['LIVE_THUMBNAILS_PUBLIC_BASE_URI']) ? $_ENV['LIVE_THUMBNAILS_PUBLIC_BASE_URI'] : null,
	// the secret if configured in the service
	"secret" => isset($_ENV['LIVE_THUMBNAILS_SERVICE_SECRET']) ? $_ENV['LIVE_THUMBNAILS_SERVICE_SECRET'] : null
);
