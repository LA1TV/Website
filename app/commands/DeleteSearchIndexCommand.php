<?php namespace uk\co\la1tv\website\commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elasticsearch;
use Config;

class DeleteSearchIndexCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'searchIndex:delete';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Deletes the search index.';

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
		$this->info('Deleting search index.');

		$esClient = Elasticsearch\ClientBuilder::create()
			->setHosts(Config::get("search.hosts"))
			->build();

		$params = ['index' => 'website'];
		$response = $esClient->indices()->delete($params);
		if ($response['acknowledged']) {
			$this->info("Index deleted!");
		}
		else {
			$this->error("Something went wrong.");
		}
		
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
		return array();
	}

}
