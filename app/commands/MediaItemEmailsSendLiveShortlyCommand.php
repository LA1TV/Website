<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\EmailTasksMediaItem;
use DB;
use Carbon;
use EmailHelpers;

class MediaItemEmailsSendLiveShortlyCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mediaItemEmails:sendLiveShortly';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Determine what live shortly emails need sending and send them.';
	
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
		$this->info('Looking for media items that are starting in 15 minutes.');
		$fifteenMinsAhead = Carbon::now()->addMinutes(15);
		$lowerBound = (new Carbon($fifteenMinsAhead))->subSeconds(90);
		$upperBound = new Carbon($fifteenMinsAhead);
		// media items which have a live stream going live in 15 minutes.
		$mediaItemsStartingInFifteen = DB::transaction(function() use (&$lowerBound, &$upperBound) {
			$mediaItemsStartingInFifteen = MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
				$q->accessible()->notLive();
			})->whereHas("emailTasksMediaItem", function($q2) {
				$q2->where("message_type_id", EmailHelpers::getMessageTypeIds()['liveInFifteen'])->where("created_at", ">=", Carbon::now()->subMinutes(15));
			}, "=", 0)->where("scheduled_publish_time", ">=", $lowerBound)->where("scheduled_publish_time", "<", $upperBound)->orderBy("scheduled_publish_time", "desc")->lockForUpdate()->get();
			
			foreach($mediaItemsStartingInFifteen as $a) {
				$emailTask = new EmailTasksMediaItem(array(
					"message_type_id"	=> EmailHelpers::getMessageTypeIds()['liveInFifteen']
				));
				// create an entry in the tasks table for the emails that are going to be sent
				$a->emailTasksMediaItem()->save($emailTask);
			}
			return $mediaItemsStartingInFifteen;
		});
		
		foreach($mediaItemsStartingInFifteen as $a) {
			$this->info("Building and sending email for media item with id ".$a->id." and name \"".$a->name."\" which is starting in 15 minutes.");
			EmailHelpers::sendMediaItemEmail($a, 'LA1:TV Live Shortly With "{title}"', "Live shortly!", "We will be streaming live in less than 15 minutes!");
			$this->info("Sent email to users.");
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
