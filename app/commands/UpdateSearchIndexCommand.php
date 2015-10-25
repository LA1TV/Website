<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elasticsearch;
use Config;
use Exception;
use DB;
use DebugHelpers;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\Show;

class UpdateSearchIndexCommand extends ScheduledCommand {

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

	private $esClient = null;
	private $coverArtWidth = null;
	private $coverArtHeight = null;

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
     * When a command should run
     *
     * @param Scheduler $scheduler
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
	public function schedule(Schedulable $scheduler)
	{
		// run every 5 minutes
		return $scheduler->everyMinutes(5);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		if (!DebugHelpers::shouldSiteBeLive()) {
			$this->info('Not running because site should not be live at the moment.');
			return;
		}

		$this->info('Updating search index.');

		$this->esClient = Elasticsearch\ClientBuilder::create()
			->setHosts(array("127.0.0.1:9200"))
			->build();

		// the width and height of images to retrieve for the cover art
		$coverArtResolutions = Config::get("imageResolutions.coverArt");
		$this->coverArtWidth = $coverArtResolutions['thumbnail']['w'];
		$this->coverArtHeight = $coverArtResolutions['thumbnail']['h'];

		// the indexes must be updated in this order to prevent future reindexes being triggered
		// e.g updating a show index may result in some playlists being queued for reindex,
		// and updating a playlst index may result in some media items being queued for reindex.
		// update/create/remove shows in index
		$this->updateShowsIndex();
		// update/create/remove playlists in index
		$this->updatePlaylistsIndex();
		// update/create/remove media items in index
		$this->updateMediaItemsIndex();
		$this->info('Done.');
	}

	private function updateMediaItemsIndex() {
		$entries = ["toAdd"=>[], "toRemove"=>[]];
		// in a transaction to make sure that version number that is returned is not one that
		// has been increased during a transaction which is stil in progress
		// mysql transaction isolation level should be REPEATABLE READ which will ensure that
		// the version number that is returned is the old one if it's currently being updated somewhere else,
		// until the update that is happening somewhere else is complete.
		$changedMediaItems = DB::transaction(function() {
			return MediaItem::with("playlists", "playlists.show")->needsReindexing()->get();
		});
		foreach($changedMediaItems as $mediaItem) {
			if ($mediaItem->getIsAccessible()) {
				$entries["toAdd"][] = ["model"=>$mediaItem, "data"=>$this->getMediaItemData($mediaItem, $this->coverArtWidth, $this->coverArtHeight)];
			}
			else {
				// this item is no longer accessible so remove it from the index
				$entries["toRemove"][] = ["model"=>$mediaItem];
			}
		}
		$this->syncIndexType("mediaItem", new MediaItem(), $entries);
	}

	private function updatePlaylistsIndex() {
		$entries = ["toAdd"=>[], "toRemove"=>[]];
		$changedPlaylists = DB::transaction(function() {
			return Playlist::with("show")->needsReindexing()->get();
		});
		foreach($changedPlaylists as $playlist) {
			if ($playlist->getIsAccessibleToPublic()) {
				$entries["toAdd"][] = ["model"=>$playlist, "data"=>$this->getPlaylistData($playlist, $this->coverArtWidth, $this->coverArtHeight)];
			}
			else {
				// this item is no longer accessible so remove it from the index
				$entries["toRemove"][] = ["model"=>$playlist];
			}
		}
		$this->syncIndexType("playlist", new Playlist(), $entries);
	}

	private function updateShowsIndex() {
		$entries = ["toAdd"=>[], "toRemove"=>[]];
		$changedShows = DB::transaction(function() {
			return Show::needsReindexing()->get();
		});
		foreach($changedShows as $show) {
			if ($show->getIsAccessible()) {
				$entries["toAdd"][] = ["model"=>$show, "data"=>$this->getShowData($show)];
			}
			else {
				// this item is no longer accessible so remove it from the index
				$entries["toRemove"][] = ["model"=>$show];
			}
		}
		$this->syncIndexType("show", new Show(), $entries);
	}

	private function updateIndexType($type, $entries) {
		if (count($entries["toAdd"]) > 0) {
			// https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_indexing_documents.html
			$params = ["body"	=> []];
			foreach($entries["toAdd"] as $a) {
				$data = $a['data'];
				$params["body"][] = [
					'index'	=> [
						'_index' => 'website',
						'_type' => $type,
						'_id' => $data["id"]
					]
				];
				$params["body"][] = $data;
				$this->info('Indexing "'.$type.'" with id '.$data["id"].'.');
			}
			$response = $this->esClient->bulk($params);

			if (count($response["items"]) !== count($entries["toAdd"]) || $response["errors"]) {
				throw(new Exception("Something went wrong indexing items."));
			}
		}
	}

	private function syncIndexType($type, $model, $entries) {
		$this->updateIndexType($type, $entries);
		// get the ids of everything stored in index
		$ids = $this->getIdsInIndexType($type);
		// get models with those ids from the datase
		$removedModelIds = $this->getNonexistantModelIds($model, $ids);
		$entryIdsToRemove = $removedModelIds;
		foreach($entries["toRemove"] as $a) {
			$model = $a["model"];
			if (!$model->in_index) {
				// we know the item is already not in the index
				continue;
			}
			$id = intval($model->id);
			if (!in_array($id, $entryIdsToRemove)) {
				$entryIdsToRemove[] = $id;
			}
		}
		$this->removeFromIndexType($type, $entryIdsToRemove);
		// this must be the last thing to happen to make sure version numbers are only updated if there are no errors
		$this->updateModelVersionNumbers($entries);
	}

	private function removeFromIndexType($type, $ids) {
		if (count($ids) > 0) {
			$params = ["body"	=> []];
			foreach($ids as $id) {
				$params["body"][] = [
					'delete'	=> [
						'_index' => 'website',
						'_type' => $type,
						'_id' => $id
					]
				];
				$this->info('Removing "'.$type.'" with id '.$id.' from index.');
			}
			$response = $this->esClient->bulk($params);
			if (count($response["items"]) !== count($ids) || $response["errors"]) {
				throw(new Exception("Something went wrong deleting items from index type."));
			}
		}
	}

	private function getIdsInIndexType($type) {
		$params = [
			'index' => 'website',
			'type' => $type,
			'body' => [
				'query' => [
					'match_all' => new \stdClass()
				],
				'fields' => []
			]
		];

		$results = $this->esClient->search($params);
		if ($results["timed_out"]) {
			throw(new Exception("Search request to get ids timed out."));
		}

		$ids = [];
		if ($results["hits"]["total"] > 0) {
			foreach($results["hits"]["hits"] as $a) {
				$ids[] = intval($a["_id"]);
			}
		}
		return $ids;
	}

	private function getNonexistantModelIds($model, $ids) {
		$foundModelIds = array_pluck($model::whereIn("id", $ids)->select("id")->get(), "id");
		return array_diff($ids, $foundModelIds);
	}

	private function updateModelVersionNumbers($entries) {
		$models = [];
		foreach($entries["toAdd"] as $a) {
			$model = $a["model"];
			$model->in_index = true;
			$models[] = $model;
		}
		foreach($entries["toRemove"] as $a) {
			$model = $a["model"];
			$model->in_index = false;
			$models[] = $model;
		}
		foreach($models as $item) {
			// the act of saving will increment the pending_search_index_version number
			// therefore set the number to 1 more than the current pending value so that after the save it's correct
			$item->current_search_index_version = intval($item->pending_search_index_version)+1;
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
