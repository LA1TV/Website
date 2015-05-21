<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\DvrLiveStreamUri;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use DebugHelpers;

class DvrBridgeServiceSendPingsCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dvrBridgeServices:sendPings';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Make the ping api call to all dvr bridge service servers that are currently being used.';
	
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
		
		$this->info('Sending ping command to any dvr bridge service servers that are being used.');
	
		// go though all dvrLiveStreamUris, get their dvr bridge service url, and send ping to it
		$dvrLiveStreamUriModels = DvrLiveStreamUri::with("liveStreamUri", "mediaItemLiveStream")->get();
		foreach($dvrLiveStreamUriModels as $a) {
			$liveStreamUriModel = $a->liveStreamUri;
			$mediaItemLiveStream = $a->mediaItemLiveStream;
			$url = $liveStreamUriModel->uri;
			$this->info("Sending ping command to dvr bridge service at url \"".$url."\".");
			$responseInfo = MediaItemLiveStream::makeDvrBridgeServiceRequest($url, "PING", intval($a->id));
			if ($responseInfo["statusCode"] !== 200) {
				// error occurred. Remove dvrLiveStreamUri as something's wrong with it.
				$a->delete();
				$this->info("Error occurred/response returned from ping command to dvr bridge service at url \"".$url."\". Removing dvr link.");
			}
			else {
				$this->info("Sent ping command to dvr bridge service at url \"".$url."\".");
			}
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
