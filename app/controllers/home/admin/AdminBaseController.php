<?php namespace uk\co\la1tv\website\controllers\home\admin;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use URL;
use Csrf;

class AdminBaseController extends BaseController {

	protected $layout = "layouts.home.admin.master";
	
	protected function setContent($content, $navPage, $cssPageId, $title=NULL) {
		$this->layout->baseUrl = URL::to("/");
		$this->layout->currentNavPage = $navPage;
		$this->layout->cssPageId = $cssPageId;
		$this->layout->title = !is_null($title) ? $title : "LA1:TV CMS";
		$this->layout->csrfToken = Csrf::getToken();
		$this->layout->content = $content;
	}

}
