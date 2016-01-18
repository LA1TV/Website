<?php

return array(
	// https://github.com/LA1TV/Website/issues/737#issue-127234219
	"enabled"		=> isset($_ENV['DEGRADED_SERVICE_ENABLED']) && $_ENV['DEGRADED_SERVICE_ENABLED']
);
