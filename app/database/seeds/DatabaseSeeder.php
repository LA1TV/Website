<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		if (App::environment() == "production") {
			$this->command->info("WARNING: You are running in production. Data will be permanently deleted if you continue.");
			if (!$this->command->confirm('Are you sure you want to continue? [y|n]: ', false))
			{
				$this->command->info("Aborting.");
				return;
			}
		}
		
		$this->call('MediaItemSeeder');
	}

}
