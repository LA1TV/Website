<?php namespace uk\co\la1tv\website\controllers\home\contact;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;

class ContactController extends HomeBaseController {

	public function getIndex() {
		$this->setContent(View::make("home.contact.index"), "contact", "contact");
	}
}
