<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use Redis;

class CheckFileStoreAvailabilityCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fileStore:checkAvailability';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check to see if the file store is available.';
	
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
		// default is run every minute
		return $scheduler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Checking to see if the file store is available.');
		if (Redis::get("fileStoreAvailableCheckRunning")) {
			$this->info('Check already in progress.');
		}
		else {
			Redis::set("fileStoreAvailableCheckRunning", true, "EX", 300);
			$filesLocation = Config::get("custom.files_location");
			$checkTime = time();
			$available = file_exists($filesLocation);
			if ($available && time() - $checkTime > 2.5) {
				// took longer than 2.5 seconds to check existance
				// presume there are issues
				$available = false;
			}
			Redis::set("fileStoreAvailable", $available, "EX", 90);
			Redis::del("fileStoreAvailableCheckRunning");
			$this->info($available ? "Available.":"Unavailable.");
		}
		$this->info("Finished.");
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
