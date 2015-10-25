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
