<?php namespace uk\co\la1tv\website\controllers\home\liveStream;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use uk\co\la1tv\website\models\LiveStream;
use App;
use View;
use Response;
use Config;
use URLHelpers;

class LiveStreamController extends HomeBaseController {

	public function getIndex($id) {

		$liveStream = LiveStream::find($id);
		if (is_null($liveStream) || !$liveStream->getShowAsLiveStream()) {
			App::abort(404);
		}

		$view = View::make("home.livestream.index");
		$view->title = $liveStream->name;
		$view->descriptionEscaped = !is_null($liveStream->description) ? nl2br(URLHelpers::escapeAndReplaceUrls($liveStream->description)) : null;
		$openGraphProperties = array(); // TODO
		$this->setContent($view, "live-stream", "live-stream", $openGraphProperties, $liveStream->name);
	}
	
	public function postPlayerInfo($liveStreamId) {
		
		$liveStream = LiveStream::find($id);
		if (is_null($liveStream) || !$liveStream->getShowAsLiveStream()) {
			App::abort(404);
		}

		$uri = null;
		$title = $liveStream->name;
		$coverArtUri = Config::get("custom.default_cover_uri");
		$embedData = null;
		$streamUris = null;
		$numWatchingNow = 0;

		$data = array(
			"uri"						=> $uri, // used for embeddable player so title can be clickable
			"title"						=> $title, // shown on embeddable player
			"coverUri"					=> $coverArtUri,
			"embedData"					=> $embedData,
			"streamUris"				=> $streamUris, // if null this means stream is not live
			"numWatchingNow"			=> $numWatchingNow
		);

		return Response::json($data);
	}

	public function missingMethod($parameters=array()) {
		// redirect /[integer]/[anything] to /index/[integer]/[anything]
		if (count($parameters) >= 1 && ctype_digit($parameters[0])) {
			return call_user_func_array(array($this, "getIndex"), $parameters);
		}
		else {
			return parent::missingMethod($parameters);
		}
	}
}
