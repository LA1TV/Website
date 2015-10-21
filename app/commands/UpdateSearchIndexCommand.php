<?php namespace uk\co\la1tv\website\commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elasticsearch;
use Config;
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

		$changedMediaItems = MediaItem::with("playlists", "playlists.show")->accessible()->needsReindexing()->get();
		
		foreach($changedMediaItems as $item) {
			$playlists = $item->playlists;
			$playlistsData = [];

			foreach($playlists as $playlist) {
				if (!$playlist->getIsAccessible()) {
					continue;
				}

				$showData = null;
				$show = $playlist->show;
				if (!is_null($show)) {
					$showData = [
						"id"			=> intval($show->id),
						"name"			=> $show->name,
						"description"	=> $show->description,
						"url"			=> $show->getUri()
					];
				}

				$playlistsData[] = [
					"generatedName"	=> $playlist->generateEpisodeTitle($item),
					"coverArtUri"	=> $playlist->getMediaItemCoverArtUri($item, $coverArtWidth, $coverArtHeight),
					"url"			=> $playlist->getMediaItemUri($item),
					"playlist"		=> [
						"id"			=> intval($playlist->id),
						"name"			=> $playlist->generateName(),
						"description"	=> $playlist->description,
						"coverArtUri"	=> $playlist->getCoverArtUri($coverArtWidth, $coverArtHeight),
						// elastic search expects milliseconds
						"scheduledPublishTime"	=> $playlist->scheduled_publish_time->timestamp * 1000,
						"seriesNo"		=> !is_null($playlist->series_no) ? intval($playlist->series_no) : null,
						"url"			=> $playlist->getUri(),
						"show"			=> $showData
					]
				];
			}

			$data = [
				"id"			=> intval($item->id),
				"name"			=> $item->name,
				"description"	=> $item->description,
				"scheduledPublishTime"	=> $item->scheduled_publish_time->timestamp * 1000,
				"playlists"		=> $playlistsData
			];

			dd($data);
		}

		// TODO get ids of everything stored in index

		// TODO get media items with those ids from the datase
		// remove anything from the index which is not in that list

		$this->info('Done.');
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
