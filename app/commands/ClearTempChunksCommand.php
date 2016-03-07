<?php namespace uk\co\la1tv\website\commands;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use uk\co\la1tv\website\models\Session as SessionModel;
use Config;

class ClearTempChunksCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'clearTempChunks:run';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clears chunks that have been uploaded that no longer belong to anyone.';
	
	// array where key is session id and value is true if session exists, false otherwise
	private $sessions = array();

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
		$this->info('Clearing temp chunks.');

		// removes any files that no longer belong to a session
		foreach (scandir(Config::get("custom.file_chunks_location")) as $filename) {
			if ($filename !== "." && $filename !== "..") {
				$parts = explode("-", $filename);
				if (count($parts) >= 2) {
					$sessionId = $parts[0];
					if (!$this->sessionExists($sessionId)) {
						// the session that created this file has expired. remove the file
						unlink(Config::get("custom.file_chunks_location") . DIRECTORY_SEPARATOR . $filename);
						Log::info("Removed \"".$filename."\"");
					}
				}
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

	private function sessionExists($sessionId) {
		if (array_key_exists($sessionId, $this->sessions)) {
			return $this->sessions[$sessionId];
		}
		return ($this->sessions[$sessionId] = !is_null(SessionModel::find($sessionId)));
	}

}
