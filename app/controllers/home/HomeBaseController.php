<?php namespace uk\co\la1tv\website\controllers\home;

use uk\co\la1tv\website\controllers\BaseController;
use URL;
use Csrf;
use Auth;
use Config;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\Playlist;


use DB; //TODO remove

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
		
		$this->layout->homeUri = Config::get("custom.base_url");
		$this->layout->guideUri = Config::get("custom.base_url") . "/guide";
		$this->layout->blogUri = Config::get("custom.blog_url");
		$this->layout->contactUri = Config::get("custom.base_url") . "/contact";
		$this->layout->aboutUri = Config::get("custom.base_url") . "/about";

		
		// recent shows in dropdown
		$shows = Show::getCachedActiveShows();
		$this->layout->showsDropdown = array();
		foreach($shows as $a) {
			$this->layout->showsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/show/".$a->id, "text"=>$a->name);
		}
		$this->layout->showsUri = Config::get("custom.base_url") . "/shows";
		
		// recent playlists dropdown
		$playlists = Playlist::getCachedActivePlaylists(false);
		$this->layout->playlistsDropdown = array();
		foreach($playlists as $a) {
			$this->layout->playlistsDropdown[] = array("uri"=>Config::get("custom.base_url") . "/playlist/".$a->id, "text"=>$a->name);
		}
		$this->layout->playlistsUri = Config::get("custom.base_url") . "/playlists";
	}

}
