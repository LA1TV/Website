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
		$view->playerInfoUri = $this->getInfoUri($liveStream->id);
		$view->registerWatchingUri = $this->getRegisterWatchingUri($liveStream->id);
		$view->loginRequiredMsg = "Please log in to use this feature.";
		$this->setContent($view, "live-stream", "live-stream", $openGraphProperties, $liveStream->name);
	}
	
	public function postPlayerInfo($id) {
		
		$liveStream = LiveStream::find($id);
		if (is_null($liveStream) || !$liveStream->getShowAsLiveStream()) {
			App::abort(404);
		}

		$id = intval($liveStream->id);
		$uri = null; // TODO
		$title = $liveStream->name;
		$coverArtUri = Config::get("custom.default_cover_uri"); // TODO allow the user to upload one
		$embedData = null; // TODO
		$streamState = $liveStream->enabled ? 1 : 0;
		$streamUris = null; // TODO
		$numWatchingNow = 0; // TODO

		$data = array(
			"id"						=> $id,
			"title"						=> $title, // shown on embeddable player
			"uri"						=> $uri, // used for embeddable player so title can be clickable
			"coverUri"					=> $coverArtUri,
			"embedData"					=> $embedData,
			"hasStream"					=> true,
			"streamState"				=> $streamState, // 0=not live, 1=live (2=show over, null=no livestream)
			"streamUris"				=> $streamUris, // if null this means stream is not live
			"numWatchingNow"			=> $numWatchingNow
		);

		return Response::json($data);
	}

	private function getInfoUri($liveStreamId) {
		return Config::get("custom.live_stream_player_info_base_uri")."/".$liveStreamId;
	}
	
	private function getRegisterWatchingUri($liveStreamId) {
		return Config::get("custom.live_stream_player_register_watching_base_uri")."/".$liveStreamId;
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
