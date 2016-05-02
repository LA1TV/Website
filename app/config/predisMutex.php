<?php

return array(
	// time in seconds before mutexes timeout.
	// should be much higher than it's expected for the longest task inside a synchronised block to take
	// The reson there has to be a timeout is so that if there's a fatal error and locks aren't removed
	// (such as a hard server reboot) when the app starts again after this period locks will be able to be
	// re-obtained again.
	"timeout"	=> 180
);
