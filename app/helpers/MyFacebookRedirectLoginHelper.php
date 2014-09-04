<?php
// thanks to http://stackoverflow.com/a/24196835/1048589

class MyFacebookRedirectLoginHelper extends \Facebook\FacebookRedirectLoginHelper {
	protected function storeState($state) {
		Session::put('fb_state', $state);
	}

	protected function loadState() {
		return $this->state = Session::get('fb_state');
	}
}