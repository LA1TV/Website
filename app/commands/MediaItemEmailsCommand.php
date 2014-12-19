<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\EmailTasksMediaItem;
use uk\co\la1tv\website\models\SiteUser;
use DB;
use Carbon;
use Config;
use View;
use URL;
use Mail;
use Facebook;

class MediaItemEmailsCommand extends ScheduledCommand {

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
		$messageTypeIds = array(
			"liveInFifteen"	=> 1, // show live in 15 minutes
			"liveNow"		=> 2  // show live/vod available now
		);
		
		
		$this->info('Looking for media items that are starting in 15 minutes.');
		$fifteenMinsAhead = Carbon::now()->addMinutes(15);
		$lowerBound = new Carbon($fifteenMinsAhead);
		$upperBound = with(new Carbon($fifteenMinsAhead))->addSeconds(90);
		// media items which have a live stream going live in 15 minutes.
		$mediaItemsStartingInFifteen = DB::transaction(function() use (&$lowerBound, &$upperBound, &$messageTypeIds) {
			$mediaItemsStartingInFifteen = MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
				$q->accessible()->notLive();
			})->whereHas("emailTasksMediaItem", function($q2) {
				$q2->where("created_at", ">=", Carbon::now()->subMinutes(15));
			}, "=", 0)->where("scheduled_publish_time", ">=", $lowerBound)->where("scheduled_publish_time", "<", $upperBound)->orderBy("scheduled_publish_time", "desc")->lockForUpdate()->get();
			
			foreach($mediaItemsStartingInFifteen as $a) {
				$emailTask = new EmailTasksMediaItem(array(
					"message_type_id"	=> $messageTypeIds['liveInFifteen']
				));
				// create an entry in the tasks table for the emails that are going to be sent
				$a->emailTasksMediaItem()->save($emailTask);
			}
			
			return $mediaItemsStartingInFifteen;
		});
		
		foreach($mediaItemsStartingInFifteen as $a) {
			$playlist = $a->getDefaultPlaylist();
			$mediaItemTitle = $playlist->generateEpisodeTitle($a);
			$this->info("Building and sending email for media item with id ".$a->id." and name \"".$a->name."\" which is starting in 15 minutes.");
			$subject = 'Live Shortly With "'.$mediaItemTitle.'"';
			$coverResolution = Config::get("imageResolutions.coverArt")['email'];
			$data = array(
				"heading"				=> "Live shortly!",
				"msg"					=> "We will be streaming live in less than 15 minutes!",
				"coverImgWidth"			=> $coverResolution['w'],
				"coverImgHeight"		=> $coverResolution['h'],
				"coverImgUri"			=> $playlist->getMediaItemCoverArtUri($a, $coverResolution['w'], $coverResolution['h']),
				"mediaItemTitle"		=> $mediaItemTitle,
				"mediaItemDescription"	=> $a->description,
				"mediaItemUri"			=> $playlist->getMediaItemUri($a),
				"facebookUri"			=> Config::get("socialMediaUris.facebook"),
				"twitterUri"			=> Config::get("socialMediaUris.twitter"),
				"contactEmail"			=> Config::get("contactEmails.general"),
				"developmentEmail"		=> Config::get("contactEmails.development"),
				"accountSettingsUri"	=> URL::route('account')
			);
			
			// get all users that have emails enabled
			$users = SiteUser::whereNotNull("fb_email")->where("email_notifications_enabled", true)->get();
			foreach($users as $user) {
				
				// attempt to update the users facebook info
				Facebook::updateUserOpenGraph($user);
				
				$email = $user->fb_email;
				// check the email hasn't become null after the facebook update and that we have permission from facebook to use the email
				// also check the last time the users details were updated successfully and if it is longer than a month ago then presume it's stale and the facebook token has expired and the user hasn't renewed it by logging back in for a long time
				$cutOffTime = with(Carbon::now())->subMonths(1);
				if ($user->hasFacebookPermission("email") && !is_null($email) && $user->fb_last_update_time->timestamp > $cutOffTime->timestamp) {
					$this->info("Sending email to user with id ".$user->id." and email \"".$email."\".");
					// send the email
					Mail::send('emails.mediaItem', $data, function($message) use (&$email, &$subject) {
						$message->to($email)->subject($subject);
					});
				}
			}
			$this->info("Sent emails.");
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
