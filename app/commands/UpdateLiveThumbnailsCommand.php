<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\LiveStreamUri;
use DebugHelpers;
use LiveThumbnails;
use Redis;
use Config;

class UpdateLiveThumbnailsCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'liveThumbnails:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Starts/stops live thumbnail generaton for live streams.';
	
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

		$liveThumbnailsServiceUri = Config::get("serviceUri");
		if (is_null($liveThumbnailsServiceUri)){
			$this->info("Live thumbnails disabled.");
			return;
		}

		if (Redis::get("liveThumbnailsUpdateRunning")) {
			$this->info('Update already in progress.');
			return;
		}
		Redis::set("liveThumbnailsUpdateRunning", true, "EX", 300);
		
		$this->info('Updating live thumbnails.');

		$liveStreamUris = LiveStreamUri::with("liveStream")->get();
		foreach($liveStreamUris as $liveStreamUri) {
			$thumbnailsEnabled = 
				!is_null($liveStreamUri->thumbnails_source_uri) &&
				$liveStreamUri->enabled &&
				$liveStreamUri->has_dvr &&
				$liveStreamUri->liveStream->enabled &&
				$liveStreamUri->liveStream->shown_as_livestream;
			if (!$thumbnailsEnabled) {
				if (!is_null($liveStreamUri->thumbnails_id)) {
					$id = $liveStreamUri->thumbnails_id;
					$liveStreamUri->thumbnails_id = null;
					$liveStreamUri->thumbnails_manifest_uri = null;
					$liveStreamUri->save();

					// if something else is using the generator don't stop it
					if (LiveStreamUri::where("thumbnails_id", $id)->count() === 0) {
						LiveThumbnails::stopGenerator($id);
					}
				}
			}
			else {
				$this->info("Updating for live steam uri with ID ".$liveStreamUri->id);
				if (is_null($liveStreamUri->thumbnails_id) || !LiveThumbnails::checkStillRunning($liveStreamUri->thumbnails_id)) {
					// it's stopped so start it again
					$liveStreamUri->thumbnails_id = null;
					$liveStreamUri->thumbnails_manifest_uri = null;	
					
					$liveStreamUriToCopy = null;
					if (is_null($liveStreamUri->thumbnails_id)) {
						// if there is already a generator running for the url then use that
						$liveStreamUriToCopy = LiveStreamUri::where("thumbnails_source_uri", $liveStreamUri->thumbnails_source_uri)
							->whereNotNull("thumbnails_id")->first();
						if (!is_null($liveStreamUriToCopy) && !LiveThumbnails::checkStillRunning($liveStreamUriToCopy->thumbnails_id)) {
							$liveStreamUriToCopy = null;
						}
					}

					if (!is_null($liveStreamUriToCopy)) {
						$liveStreamUri->thumbnails_id = $liveStreamUriToCopy->thumbnails_id;
						$liveStreamUri->thumbnails_manifest_uri = $liveStreamUriToCopy->thumbnails_manifest_uri;
					}
					else {
						$result = LiveThumbnails::startGenerator($liveStreamUri->thumbnails_source_uri);
						if (!is_null($result)) {
							$id = $result["id"];
							$manifestUri = $result["manifestUri"];
							$liveStreamUri->thumbnails_id = $id;
							$liveStreamUri->thumbnails_manifest_uri = $manifestUri;
						}
					}
					$liveStreamUri->save();
				}					
			}
		}
		Redis::del("liveThumbnailsUpdateRunning");
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
