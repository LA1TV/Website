<?php

return array(
	// the url to the service running https://github.com/tjenkinson/hls-live-thumbnails
	// live thumbnails disabled if null
	"liveThumbnailsServiceUri"	=> isset($_ENV['LIVE_THUMBNAILS_SERVICE_URI']) ? $_ENV['LIVE_THUMBNAILS_SERVICE_URI'] : null
);
