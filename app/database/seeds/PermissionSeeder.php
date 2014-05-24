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
			array("id"=>1, "name"=>"Log In", "description"=>"Allows log in to the CMS."),
			array("id"=>2, "name"=>"Permissions", "description"=>"Allows managing user permissions."),
			array("id"=>3, "name"=>"Live Streams", "description"=>"Allows configuration of live streams."),
			array("id"=>4, "name"=>"Site Users", "description"=>"Allows managing of registered site users."),
			array("id"=>5, "name"=>"Site Comments", "description"=>"Allows managing site comments and commenting as station.")
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
