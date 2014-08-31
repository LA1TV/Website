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
					array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0))),
			array("name"=>"Content Management", "description"=>"Allows managing media items.", "permissionIdsFlags"=>
					array(array(1, 1))),
			array("name"=>"Shows Management", "description"=>"Allows managing shows.", "permissionIdsFlags"=>
					array(array(2, 1))),
			array("name"=>"Playlist Management", "description"=>"Allows managing playlists.",  "permissionIdsFlags"=>
					array(array(3, 1), array(2, 0), array(1, 0))),
			array("name"=>"Stream Management", "description"=>"Allows managing live streams.", "permissionIdsFlags"=>
					array(array(4, 1))),
			array("name"=>"Site Users Management", "description"=>"Allows managing site users.", "permissionIdsFlags"=>
					array(array(5, 1))),
			array("name"=>"CMS Users Management", "description"=>"Allows managing the CMS users.", "permissionIdsFlags"=>
					array(array(6, 1))),
			array("name"=>"Comments Management", "description"=>"Allows managing site comments and commenting as station.", "permissionIdsFlags"=>
					array(array(7, 0)))
		);
		
		foreach($groups as $i=>$a) {
			$permissionIdsFlags = $a['permissionIdsFlags'];
			unset($a['permissionIdsFlags']);
			$a['position'] = $i;
			
			$permissionIds = array();
			foreach($permissionIdsFlags as $b) {
				$permissionIds[] = $b[0];
			}
			
			$permissions = Permission::whereIn("id", $permissionIds)->get();
			
			$group = new PermissionGroup($a);
			DB::transaction(function() use(&$group, &$permissions, &$permissionIdsFlags) {
				$group->save();
				foreach ($permissionIdsFlags as $b) {
					$flag = $b[1];
					$permissionId = $b[0];
					$group->permissions()->attach($permissions->find($permissionId), array("permission_flag"=>$flag));
				}
			});
		}
		$this->command->info('Permissions groups created and permissions assigned to groups!');
	}
}
