<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DebugHelpers;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\Playlist;

class SearchIndexCheckForItemsCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'searchIndex:checkForItems';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks for items that should be reindexed and marks them for reindexing.';

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

		$this->info('Checking for items that should be reindexed.');
		$this->checkPlaylistsForReindex();
		$this->checkMediaItemsForReindex();

		$this->info('Done.');
	}

	private function checkPlaylistsForReindex() {
		// check for media items which which should be reindexed because their accessibiliy has changed
		$playlistsToReindex = Playlist::upToDateInIndex()->where(function($q) {
			$q->where(function($q) {
				$q->accessibleToPublic()->where("in_index", false);
			})->orWhere(function($q) {
				$q->notAccessibleToPublic()->where("in_index", true);
			});
		})->get();

		foreach($playlistsToReindex as $a) {
			// touching will increment the version number
			$a->touch();
			$this->info("Playlist with id ".$a->id." queued for reindex.");
		}
	}

	private function checkMediaItemsForReindex() {
		// check for media items which which should be reindexed because their accessibiliy has changed
		$mediaItemsToReindex = MediaItem::upToDateInIndex()->where(function($q) {
			$q->where(function($q) {
				$q->accessible()->where("in_index", false);
			})->orWhere(function($q) {
				$q->notAccessible()->where("in_index", true);
			});
		})->get();

		foreach($mediaItemsToReindex as $a) {
			// touching will increment the version number
			$a->touch();
			$this->info("Media item with id ".$a->id." queued for reindex.");
		}
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
