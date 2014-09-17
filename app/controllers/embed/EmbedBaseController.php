<?php namespace uk\co\la1tv\website\controllers\embed;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Config;
use Facebook;

class EmbedBaseController extends BaseController {

	protected $layout = "layouts.embed.master";
	
	protected function setContent($content, $cssPageId, $title=NULL) {
		$this->layout->baseUrl = URL::to("/");
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV";
		$this->layout->description = ""; // TODO
		$this->layout->content = $content;
		$this->layout->allowRobots = false;
		$this->layout->stylesheetApplicationPath = "includes/embed/application";
		$this->layout->javascriptApplicationPath = "includes/embed/application";
		$this->layout->pageData = array(
			"baseUrl"		=> URL::to("/"),
			"assetsBaseUrl"	=> asset(""),
			"csrfToken"		=> Csrf::getToken(),
			"loggedIn"		=> Facebook::isLoggedIn()
		);
	}

}
