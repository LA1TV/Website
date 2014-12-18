<?php namespace uk\co\la1tv\website\controllers\home\contact;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use Config;

class ContactController extends HomeBaseController {

	public function getIndex() {
		$view = View::make("home.contact.index");
		$view->contactEmail = Config::get("contactEmails.general");
		$view->developmentEmail = Config::get("contactEmails.development");
		$view->facebookPageUri = Config::get("socialMediaUris.facebook");
		$view->twitterPageUri = Config::get("socialMediaUris.twitter");
		$this->setContent($view, "contact", "contact", array(), "Contact");
	}
}
