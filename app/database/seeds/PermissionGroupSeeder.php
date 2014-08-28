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
			array("name"=>"Series Management", "description"=>"Allows managing series.", "permissionIdsFlags"=>
					array(array(2, 1))),
			array("name"=>"Playlist Management", "description"=>"Allows managing playlists.",  "permissionIdsFlags"=>
					array(array(3, 1))),
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
			$permissionFlags = array();
			foreach($permissionIdsFlags as $b) {
				$permissionIds[] = $b[0];
				$permissionFlags[] = $b[1];
			}
			
			
			$permissions = Permission::whereIn("id", $permissionIds)->get();
			
			$group = new PermissionGroup($a);
			db::transaction(function() use(&$group, &$permissions, &$permissionFlags) {
				$group->save();
				foreach ($permissions as $c=>$b) {
					$flag = $permissionFlags[$c];
					$group->permissions()->attach($b, array("permission_flag"=>$flag));
				}
			});
		}
		$this->command->info('Permissions groups created and permissions assigned to groups!');
	}
}
