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
use DebugHelpers;

class MediaItemEmailsSendVodAvailableCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mediaItemEmails:sendVodAvailable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Determine what VOD available emails need sending and send them.';
	
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
		// media items which have vod which is accessible, have the sent_vod_available_email flag set to 0, and have not had a email about this sent recently
		$mediaItems = DB::transaction(function() {
			
			$mediaItemsWantingEmail = array();
			
			$mediaItems = MediaItem::with("emailTasksMediaItem")->accessible()->whereHas("videoItem", function($q) {
				$q->live();
			})->where("email_notifications_enabled", true)->where("sent_vod_available_email", false)->lockForUpdate()->get();
			
			foreach($mediaItems as $a) {
				
				// set the flag to say that this has been looked at and an email will probably be sent.
				$a->sent_vod_available_email = true;
				$a->save();
				
				$emailSentRecently = $a->emailTasksMediaItem()->where("created_at", ">=", Carbon::now()->subMinutes(15))->count() > 0;
				
				if ($emailSentRecently) {
					continue;
				}
				
				$mediaItemsWantingEmail[] = $a;
			
				$emailTask = new EmailTasksMediaItem(array(
					"message_type_id"	=> EmailHelpers::getMessageTypeIds()['availableNow']
				));
				// create an entry in the tasks table for the emails that are going to be sent
				$a->emailTasksMediaItem()->save($emailTask);
			}
			return $mediaItemsWantingEmail;
		});
		
		foreach($mediaItems as $a) {
			$this->info("Building and sending email for media item with id ".$a->id." and name \"".$a->name."\" which has VOD which has now gone live.");
			EmailHelpers::sendMediaItemEmail($a, '"{title}" Available Now From LA1:TV', "New content available!", "We now have new content available to watch on demand at our website.");
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
