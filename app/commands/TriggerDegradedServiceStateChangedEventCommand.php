<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use Redis;
use Event;
use DebugHelpers;

class TriggerDegradedServiceStateChangedEventCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'events:triggerDegradedServiceStateChanged';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Determine if the degraded service state has changed and fire the event if it has.';
	
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
		if (!DebugHelpers::shouldSiteBeLive()) {
			$this->info('Not running because site should not be live at the moment.');
			return;
		}

		$this->info('Checking to see if the degraded service state has changed.');

		$degradedServiceEnabled = Config::get("degradedService.enabled");
		$degradedServiceWasEnabled = (boolean) Redis::get("degradedServiceEnabled");
		Redis::set("degradedServiceEnabled", $degradedServiceEnabled);

		if ($degradedServiceEnabled !== $degradedServiceWasEnabled) {
			// fire an event because degraded service state has changed
			$this->info("Firing event because degraded service state has changed.");
			Event::fire('degradedService.stateChanged', [$degradedServiceEnabled]);
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
