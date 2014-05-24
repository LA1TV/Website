<?php

use uk\co\la1tv\website\models\Permission;
use uk\co\la1tv\website\models\PermissionGroup;

class PermissionGroupSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$groups = array(
			array("name"=>"Read Only", "description"=>"Ability to view most of the settings in the cms.", "permissionIdsFlags"=>
					array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(7, 0), array(8, 0))),
			array("name"=>"Stream Manager", "description"=>"Allows managing live streams.", "permissionIdsFlags"=>
					array(array(3, 1))),
			array("name"=>"Uploads", "description"=>"Allows uploading videos to the server.", "permissionIdsFlags"=>
					array(array(6, 0))),
			array("name"=>"Content Management", "description"=>"Allows managing video/stream items in playlists.", "permissionIdsFlags"=>
					array(array(7, 1))),
			array("name"=>"Content Creator", "description"=>"Allows creating playlists.",  "permissionIdsFlags"=>
					array(array(8, 1))),
			array("name"=>"Full Access", "description"=>"Allows full access to the cms.",  "permissionIdsFlags"=>
					array(array(1, 1), array(2, 1), array(3, 1), array(4, 1), array(5, 1), array(6, 0), array(7, 1), array(8, 1)))
		);
		
		foreach($groups as $a) {
			$permissionIdsFlags = $a['permissionIdsFlags'];
			unset($a['permissionIdsFlags']);
			
			$permissionIds = array();
			$permissionFlags = array();
			foreach($permissionIdsFlags as $b) {
				$permissionIds[] = $b[0];
				$permissionFlags[] = $b[1];
			}
			
			
			$permissions = Permission::whereIn("id", $permissionIds)->get();
			
			$group = new PermissionGroup($a);
			foreach ($permissions as $c=>$b) {
				$flag = $permissionFlags[$c];
				$group->save();
				$group->permissions()->attach($b, array("permission_flag"=>$flag));
			}
		}
		$this->command->info('Permissions groups created and permissions assigned to groups!');
	}


}
