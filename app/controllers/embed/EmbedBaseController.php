<?php namespace uk\co\la1tv\website\controllers\embed;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;
use Facebook;
use Request;

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
			"baseurl"		=> URL::to("/"),
			"assetsbaseurl"	=> asset(""),
			"csrftoken"		=> Csrf::getToken(),
			"loggedin"		=> Facebook::isLoggedIn() ? "1" : "0"
		);
	}

}
