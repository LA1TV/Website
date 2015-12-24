<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\MediaItem;
use DB;
use DebugHelpers;
use Event;

class TriggerVODAvailableEventCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'events:triggerVODAvailable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Determine what VOD available events need triggering and trigger them.';
	
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
		
		$this->info('Looking for media items which contain VOD which has just gone live.');
		// media items which have vod which is accessible, and have the available_event_triggered flag set to 0
		$mediaItems = DB::transaction(function() {
			
			$mediaItemVideosWantingEvent = array();

			$mediaItems = MediaItem::with("videoItem")->accessible()->whereHas("videoItem", function($q) {
				$q->live()->where("available_event_triggered", false);
			})->lockForUpdate()->get();
			
			foreach($mediaItems as $a) {
				// set the flag to say that this has been looked at and an event will probably be triggered.
				$mediaItemVideo = $a->videoItem;
				$mediaItemVideo->available_event_triggered = true;
				if ($mediaItemVideo->save()) {
					$mediaItemVideosWantingEvent[] = $mediaItemVideo;
				}
			}
			return $mediaItemVideosWantingEvent;
		});

		foreach ($mediaItems as $a) {
			$this->info("Triggering vod available event for media item video with id ".$a->id.".");
			Event::fire('mediaItemVideo.available', array($a));
			$this->info("Triggered vod available event for media item video with id ".$a->id.".");
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
