<?php namespace uk\co\la1tv\website\controllers\home\ajax;

use uk\co\la1tv\website\controllers\BaseController;
use View;
use Response;
use FormHelpers;
use Config;
use Session;
use Elasticsearch;
use App;

class AjaxController extends BaseController {

	public function postTime() {
		return Response::json(array(
			"time"	=> microtime(true)
		));
	}
	
	// used as an endpoint to ping to keep a users session alive
	public function postHello() {
		return Response::json(array(
			"data"	=> "hi"
		));
	}
	
	// retrieves log data from javascript running in the clients
	public function postLog() {
	
		$logger = $this->getLogValue(FormHelpers::getValue("logger"));
		$timestamp = $this->formatLogDate(FormHelpers::getValue("timestamp"), true);
		$level = $this->getLogValue(FormHelpers::getValue("level"));
		$url = $this->getLogValue(FormHelpers::getValue("url"));
		$debugId = $this->getLogValue(FormHelpers::getValue("debug_id"));
		$message = $this->getLogValue(FormHelpers::getValue("message"), true);
	
		$logStr = "Server time: ".$this->formatLogDate(time())."  Session id: \"".Session::getId()."\"  Log level: ".$level."  Client time: ".$timestamp."  Url: ".$url."  Debug id: ".$debugId."  Message: ".$message;
		
		// append to the js log file.
		file_put_contents(Config::get("custom.js_log_file_path"), $logStr . "\r\n", FILE_APPEND | LOCK_EX);
		
		return Response::json(array("success"=>true));
	}

	public function postSearch() {
		$enabled = Config::get("search.enabled");

		if (!$enabled) {
			return App::abort(404);
		}

		$term = isset($_POST["term"]) ? $_POST["term"] : "";

		$client = Elasticsearch\ClientBuilder::create()
		->setHosts(Config::get("search.hosts"))
		->build();

		$params = [
			'index' => 'website',
			'type' => 'mediaItem',
			'body' => [
				'query' => [
					'dis_max' => [
						'tie_breaker' => 0.3,
						'queries' => [
							[
								'dis_max' => [
									'tie_breaker' => 0.3,
									'queries' => [
										[
											'multi_match' => [
												'query' => $term,
												'type' => 'most_fields',
												'fields' => ['name^10', 'name.std'],
												'boost' => 13
											]
										],
										[
											'multi_match' => [
												'query' => $term,
												'type' => 'most_fields',
												'fields' => ['description^10', 'description.std'],
												'boost' => 11
											]
										]
									]
								]
							],
							[
								'nested' => [
									'path' => 'playlists.playlist',
									'query' => [
										'dis_max' => [
											'tie_breaker' => 0.3,
											'queries' => [
												[
													'multi_match' => [
														'query' => $term,
														'type' => 'most_fields',
														'fields' => ['playlists.playlist.name^10', 'playlists.playlist.name.std'],
														'boost' => 8
													]
												],
												[
													'multi_match' => [
														'query' => $term,
														'type' => 'most_fields',
														'fields' => ['playlists.playlist.description^10', 'playlists.playlist.description.std'],
														'boost' => 6
													]
												]
											]
										]
									]
								]
							],
							[
								'nested' => [
									'path' => 'playlists.playlist.show',
									'query' => [
										'dis_max' => [
											'tie_breaker' => 0.3,
											'queries' => [
												[
													'multi_match' => [
														'query' => $term,
														'type' => 'most_fields',
														'fields' => ['playlists.playlist.show.name^10', 'playlists.playlist.show.name.std'],
														'boost' => 3
													]
												],
												[
													'multi_match' => [
														'query' => $term,
														'type' => 'most_fields',
														'fields' => ['playlists.playlist.show.description^10', 'playlists.playlist.show.description.std'],
														'boost' => 1
													]
												]
											]
										]
									]
								]
							]
						]
					]
				]
			]
		];
		
		$result = $client->search($params);
		if ($result["timed_out"]) {
			App::abort(500); // server error
		}

		$results = array();
		if ($result["hits"]["total"] > 0) {
			foreach($result["hits"]["hits"] as $hit) {
				$source = $hit["_source"];
				$result = array(
					"title"			=> $source["name"],
					"description"	=> $source["description"],
					"thumbnailUri"	=> $source["playlists"][0]["coverArtUri"],
					"url"			=> $source["playlists"][0]["url"]
				);
				$results[] = $result;
			}
		}
		
		return Response::json(array(
			"results"	=> $results
		));
	}
	
	private function formatLogDate($a, $milliseconds=false) {
		$a = intval($a);
		if (is_null($a)) {
			return "[Invalid Date]";
		}
		if ($milliseconds) {
			 $a = floor($a/1000);
		}
		return '"'.date(DATE_RFC2822, $a).'"';
	}
	
	private function getLogValue($a, $quotesAllowed=false) {
		$str = "[None]";
		
		if (!$quotesAllowed && strpos($a, '"') !== FALSE) {
			// " are not allowed in the value as it's only " that distinguish the separate parts of the log. It's fine in the message as that's the last thing in the log line
			$str = "[Invalid]";
		}
		else if (!is_null($a)) {
			$str = '"'.$a.'"';
		}
		return $str;
	}
}
