<?php namespace uk\co\la1tv\website\commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Redis;

class SendPushNotificationCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'pushNotifications:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a push notification.';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Sending...');

		$title = $this->option('title');
		$body = $this->option('body');
		$ttl = 60;
		$iconUrl = asset("assets/img/notification-icon.png");
		$this->sendToRedis(array(
			"title"	=> $title,
			"body"	=> $body,
			"ttl"	=> $ttl,
			"iconUrl"	=> $iconUrl
		));
		$this->info('Done.');
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
		return array(
			array("title", "t", InputOption::VALUE_OPTIONAL, "Notification title.", "Test Notification"),
			array("body", "b", InputOption::VALUE_OPTIONAL, "Notification body.", "This is a test from LA1TV.")
		);
	}

	private function sendToRedis($payload) {
		$data = array(
			"eventId"	=> "custom",
			"payload"	=> $payload
		);
		$redis = Redis::connection();
		$payload = json_encode($data);
		$this->info($payload);
		$redis->publish("siteNotificationsChannel", $payload);
	}

}
