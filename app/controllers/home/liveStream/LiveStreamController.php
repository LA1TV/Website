<?php namespace uk\co\la1tv\website\controllers\home\liveStream;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use uk\co\la1tv\website\models\LiveStream;
use App;
use View;
use Response;
use Config;
use URLHelpers;
use Auth;

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
		$liveStream->load("watchingNows");

		// true if a user is logged into the cms and has permission to view live streams.
		$userHasLiveStreamsPermission = false;
		if (Auth::isLoggedIn()) {
			$userHasLiveStreamsPermission = Auth::getUser()->hasPermission(Config::get("permissions.liveStreams"), 0);
		}

		$streamAccessible = $liveStream->getIsAccessible();

		$id = intval($liveStream->id);
		$uri = $liveStream->getUri();
		$title = $liveStream->name;
		$coverArtUri = Config::get("custom.default_cover_uri"); // TODO allow the user to upload one
		$embedData = null; // TODO
		$streamState = $streamAccessible ? 2 : 1;
		$minNumWatchingNow = Config::get("custom.min_num_watching_now");
		$numWatchingNow = $liveStream->getNumWatchingNow();
		if (!$userHasLiveStreamsPermission && $numWatchingNow < $minNumWatchingNow) {
			$numWatchingNow = null;
		}
		$streamUris = array();

		if ($streamAccessible) {
			foreach($liveStream->getQualitiesWithUris("live") as $qualityWithUris) {
				$streamUris[] = array(
					"quality"	=> array(
						"id"	=> intval($qualityWithUris['qualityDefinition']->id),
						"name"	=> $qualityWithUris['qualityDefinition']->name
					),
					"uris"		=> $qualityWithUris['uris']
				);
			}
		}

		$data = array(
			"id"						=> $id,
			"title"						=> $title, // shown on embeddable player
			"uri"						=> $uri, // used for embeddable player so title can be clickable
			"coverUri"					=> $coverArtUri,
			"embedData"					=> $embedData,
			"hasStream"					=> true,
			"streamState"				=> $streamState, // 1=not live, 2=live (3=show over, null=no livestream)
			"streamUris"				=> $streamUris, // if null this means stream is not live
			"numWatchingNow"			=> $numWatchingNow
		);

		return Response::json($data);
	}

	public function postRegisterWatching($liveStreamId) {
		$liveStream = LiveStream::accessible()->find($liveStreamId);
		if (is_null($liveStream)) {
			App::abort(404);
		}
		
		$success = $liveStream->registerWatching();
		return Response::json(array("success"=>$success));
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
