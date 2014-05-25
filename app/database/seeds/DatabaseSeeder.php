<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		if (App::environment() == "production") {
			$this->command->error("WARNING: You are running in production. Data will be permanently deleted if you continue.");
			if (!$this->command->confirm('Are you sure you want to continue? [y|n]:', false))
			{
				$this->command->comment("Aborting.");
				return;
			}
		}
		
		$this->call('TruncateTablesSeeder');
		$this->call('PermissionSeeder');
		$this->call('PermissionGroupSeeder');
		$this->call('QualityDefinitionsSeeder');
		$this->call('UserSeeder');
		$this->call('SiteUsersSeeder');
		$this->call('FilesSeeder');
		$this->call('MediaItemSeeder');
		$this->call('VideoFilesSeeder');
		$this->call('PlaylistsSeeder');
	}

}
