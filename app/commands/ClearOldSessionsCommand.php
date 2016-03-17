<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use DebugHelpers;
use uk\co\la1tv\website\models\Session as SessionModel;

class ClearOldSessionsCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'clearOldSessions:run';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove expired sessions from the database.';
	
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
		return $scheduler->everyMinutes(15);
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

		$this->info('Removing expired sessions.');
		$cutOffTime = time() - (Config::get("session.lifetime")*60);
		SessionModel::where('last_activity', '<=', $cutOffTime)->delete();
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
