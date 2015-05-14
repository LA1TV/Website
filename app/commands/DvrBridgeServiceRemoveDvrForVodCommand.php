<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\MediaItemVideo;
use DebugHelpers;

class DvrBridgeServiceRemoveDvrForVodCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dvrBridgeServices:removeDvrForVod';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Removed any dvr recordings for media items where vod has gone live.';
	
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
		
		$this->info('Removing any dvr recordings where there is vod that has gone live.');
	
		$mediaItemVideoModels = MediaItemVideo::with("mediaItem", "mediaItem.liveStreamItem")->live()->whereHas("mediaItem", function($q) {
			$q->whereHas("liveStreamItem", function($q2) {
				$q2->live(false)->has("dvrLiveStreamUris");
			});
		})->get();
		foreach($mediaItemVideoModels as $a) {
			$mediaItemLiveStream = $a->mediaItem->liveStreamItem;
			$this->info("Requesting deletion of any dvr recordings for media item live stream with id ".$mediaItemLiveStream->id." as media item now has live vod.");
			$mediaItemLiveStream->removeDvrs();
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
