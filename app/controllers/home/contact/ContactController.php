<?php namespace uk\co\la1tv\website\controllers\home\contact;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use Config;

class ContactController extends HomeBaseController {

	public function getIndex() {

		$twitterProperties = array();
		$twitterProperties[] = array("name"=> "card", "content"=> "summary");
		
		$openGraphProperties = array();
		$description = "Get in touch with the team.";
		$twitterProperties[] = array("name"=> "description", "content"=> str_limit($description, 197, "..."));
		$openGraphProperties[] = array("name"=> "og:description", "content"=> $description);
		
		$title = "Contact Us";
		$twitterProperties[] = array("name"=> "title", "content"=> $title);
		$openGraphProperties[] = array("name"=> "og:title", "content"=> $title);
		$view = View::make("home.contact.index");
		$view->contactEmail = Config::get("contactEmails.general");
		$view->developmentEmail = Config::get("contactEmails.development");
		$view->facebookPageUri = Config::get("socialMediaUris.facebook");
		$view->twitterPageUri = Config::get("socialMediaUris.twitter");
		$view->twitterWidgetId = Config::get("twitter.timeline_widget_id");
		$this->setContent($view, "contact", "contact", $openGraphProperties, $title, 200, $twitterProperties);
	}
}
