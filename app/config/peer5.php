<?php

return array(
	// disabled if null
	// note stream domains need to be configured at https://app.peer5.com/user
	"apiKey"	=> isset($_ENV['PEER_5_API_KEY']) ? $_ENV['PEER_5_API_KEY'] : null
);
