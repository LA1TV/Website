<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;

class HomeBaseController extends BaseController {

	protected $layout = "layouts.home.master";
	
	protected function setContent($content, $navPage, $cssPageId, $title=NULL) {
		$this->layout->baseUrl = URL::to("/");
		$this->layout->currentNavPage = $navPage;
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV";
		$this->layout->csrfToken = Csrf::getToken();
		$this->layout->description = ""; // TODO
		$this->layout->content = $content;
	}

}
