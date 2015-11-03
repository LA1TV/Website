<?php namespace uk\co\la1tv\website\commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elasticsearch;
use Config;

class CreateSearchIndexCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'searchIndex:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Creates the search index.';

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
		$this->info('Creating search index.');

		$esClient = Elasticsearch\ClientBuilder::create()
			->setHosts(Config::get("search.hosts")))
			->build();

		$showProperties = [
			'id' => [
				'type' => 'integer',
				'index' => 'no'
			],
			'name' => [
				'type' => 'string'
			],
			'description' => [
				'type' => 'string'
			],
			'url' => [
				'type' => 'string',
				'index' => 'no'
			]
		];

		$playlistProperties = [
			'id' => [
				'type' => 'integer',
				'index' => 'no'
			],
			'name' => [
				'type' => 'string'
			],
			'description' => [
				'type' => 'string'
			],
			'scheduledPublishTime' => [
				'type' => 'date'
			],
			'coverArtUri' => [
				'type' => 'string',
				'index' => 'no'
			],
			'seriesNo' => [
				'type' => 'integer',
				'index' => 'no'
			],
			'url' => [
				'type' => 'string',
				'index' => 'no',
			],
			'show' => [
				'type' => 'nested',
				'properties' => $showProperties
			]
		];


		$mediaItemProperties = [
			'id' => [
				'type' => 'integer',
				'index' => 'no'
			],
			'name' => [
				'type' => 'string'
			],
			'description' => [
				'type' => 'string'
			],
			'scheduledPublishTime' => [
				'type' => 'date'
			],
			'playlists' => [
				'type' => 'nested',
				'properties' => [
					'generatedName' => [
						'type' => 'string'
					],
					'coverArtUri' => [
						'type' => 'string',
						'index' => 'no'
					],
					'url' => [
						'type' => 'string',
						'index' => 'no'
					],
					'playlist' => [
						'type' => 'nested',
						'properties' => $playlistProperties
					]
				]
			]
		];

		// creating 3 indexes "mediaItem", "playlist" and "show".
		// the "mediaItem" index contains copies of data in "playlist" and "show" and
		// the "playlist" index contains copies of data in "show"
		// this duplication is more optimum for searching according to https://www.elastic.co/guide/en/elasticsearch/guide/current/denormalization.html

		// https://www.elastic.co/guide/en/elasticsearch/guide/current/root-object.html
		$params = [
			'index' => 'website',
			'body' => [
				'mappings' => [
					// _default_ settings apply to all types below
					'_default_' => [
						'dynamic' => "strict", // throw an exception if an unknown field is encountered
						'include_in_all' => false, // don't create _all and include everything in special _all field
					],
					'mediaItem' => [
						'properties' => $mediaItemProperties,
					],
					'playlist' => [
						'properties' => $playlistProperties,
					],
					'show' => [
						'properties' => $showProperties,
					]
				]
			]
		];

		$response = $esClient->indices()->create($params);
		if ($response['acknowledged']) {
			$this->info("Index created!");
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
