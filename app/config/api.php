<?php

return array(
	// if false then the api will return a service unavailable response.
	"enabled"		=> isset($_ENV['API_ENABLED']) && $_ENV['API_ENABLED']
);
