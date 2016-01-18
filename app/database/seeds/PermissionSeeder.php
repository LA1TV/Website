<?php

use uk\co\la1tv\website\models\Permission;

class PermissionSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = array(
			array("id"=>1, "name"=>"Media Items", "description"=>"Allows managing media items."),
			array("id"=>2, "name"=>"Shows", "description"=>"Allows managing shows."),
			array("id"=>3, "name"=>"Playlists", "description"=>"Allows managing playlists."),
			array("id"=>4, "name"=>"Live Streams", "description"=>"Allows configuration of live streams."),
			array("id"=>5, "name"=>"Site Users", "description"=>"Allows managing of registered site users."),
			array("id"=>6, "name"=>"CMS Users", "description"=>"Allows managing CMS users."),
			array("id"=>7, "name"=>"Site Comments", "description"=>"Allows managing site comments and commenting as station."),
			array("id"=>8, "name"=>"API Users", "description"=>"Allows managing API users.")
		);
		
		foreach($permissions as $a) {
			$p = Permission::find($a['id']);
			if ($p !== NULL) {
				unset($a['id']);
				$p->update($a);
			}
			else {
				Permission::create($a);
			}
		}
		$this->command->info('Permissions created/updated!');
	}


}
