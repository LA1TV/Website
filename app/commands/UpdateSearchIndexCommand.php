<?php namespace uk\co\la1tv\website\commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elasticsearch;
use Config;
use Exception;
use uk\co\la1tv\website\models\MediaItem;

class UpdateSearchIndexCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'searchIndex:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates the search index.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Updating search index.');

		$esClient = Elasticsearch\ClientBuilder::create()
			->setHosts(array("127.0.0.1:9200"))
			->build();

		// TODO update/create/remove media items in index

		// the width and height of images to retrieve for the cover art
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		$coverArtWidth = $coverArtResolutions['thumbnail']['w'];
		$coverArtHeight = $coverArtResolutions['thumbnail']['h'];

		$entries = [];
		$changedMediaItems = MediaItem::with("playlists", "playlists.show")->accessible()->needsReindexing()->get();
		foreach($changedMediaItems as $mediaItem) {
			$entries[] = $this->getMediaItemData($mediaItem, $coverArtWidth, $coverArtHeight);
		}

		$this->updateIndexType("mediaItem", $entries);
		$this->updateModelVersionNumbers($changedMediaItems);

		// TODO get ids of everything stored in index

		// TODO get media items with those ids from the datase
		// remove anything from the index which is not in that list

		// TODO playlists index
		// TODO shows index

		$this->info('Done.');
	}

	private function updateIndexType($type, $entries) {
		if (count($entries) > 0) {
			// https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_indexing_documents.html
			$params = ["body"	=> []];
			foreach($entries as $a) {
				$params["body"][] = [
					'index'	=> [
						'_index' => 'website',
						'_type' => $type,
						'_id' => $a["id"]
					]
				];
				$params["body"][] = $a;
			}
			$response = $esClient->bulk($params);

			if (count($response["items"]) !== count($entries) || $response["errors"]) {
				throw(new Exception("Something went wrong indexing media items."));
			}
		}
	}

	private function updateModelVersionNumbers($models) {
		foreach($models as $item) {
			$item->current_search_index_version = intval($item->pending_search_index_version);
			if (!$item->save()) {
				throw(new Exception("Error updating model version numbers."));
			}
		}
	}

	private function getShowData($show) {
		return [
			"id"			=> intval($show->id),
			"name"			=> $show->name,
			"description"	=> $show->description,
			"url"			=> $show->getUri()
		];
	}

	private function getPlaylistData($playlist, $coverArtWidth, $coverArtHeight) {
		$showData = null;
		$show = $playlist->show;
		if (!is_null($show)) {
			$showData = $this->getShowData($show);
		}

		return [
			"id"			=> intval($playlist->id),
			"name"			=> $playlist->generateName(),
			"description"	=> $playlist->description,
			"coverArtUri"	=> $playlist->getCoverArtUri($coverArtWidth, $coverArtHeight),
			// elastic search expects milliseconds
			"scheduledPublishTime"	=> $playlist->scheduled_publish_time->timestamp * 1000,
			"seriesNo"		=> !is_null($playlist->series_no) ? intval($playlist->series_no) : null,
			"url"			=> $playlist->getUri(),
			"show"			=> $showData
		];
	}

	private function getMediaItemData($mediaItem, $coverArtWidth, $coverArtHeight) {
		$playlists = $mediaItem->playlists;
		$playlistsData = [];
		foreach($playlists as $playlist) {
			if (!$playlist->getIsAccessible()) {
				continue;
			}
			$playlistsData[] = [
				"generatedName"	=> $playlist->generateEpisodeTitle($mediaItem),
				"coverArtUri"	=> $playlist->getMediaItemCoverArtUri($mediaItem, $coverArtWidth, $coverArtHeight),
				"url"			=> $playlist->getMediaItemUri($mediaItem),
				"playlist"		=> $this->getPlaylistData($playlist, $coverArtWidth, $coverArtHeight)
			];
		}

		return [
			"id"			=> intval($mediaItem->id),
			"name"			=> $mediaItem->name,
			"description"	=> $mediaItem->description,
			"scheduledPublishTime"	=> $mediaItem->scheduled_publish_time->timestamp * 1000,
			"playlists"		=> $playlistsData
		];
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
