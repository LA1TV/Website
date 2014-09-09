<?php

return array(
	// interval that must occur between login attempts in seconds
	"attemptInterval"			=> 8,
	"cosignEnabled"				=> false,
	"cosignServiceName"			=> "cosign-http-www2.la1tv.co.uk",
	"cosignFilterDbLocation"	=> "/var/cosign/filter",
	// time in seconds that cosign cookies are considered valid after being created before re-authentication needed
	"cosignCookieDuration"		=> 300
);	
