<?php

use uk\co\la1tv\website\models\ProductionRole;
use uk\co\la1tv\website\models\ProductionRoleMediaItem;
use uk\co\la1tv\website\models\ProductionRolePlaylist;

class ProductionRolesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$roles = [
			// name, description, valid for media item ([name override, description override]), valid for playlist ([name override, description override])
			["Role 1", "Role 1 Description", ["Role 1 Title Override", null], null],
			["Role 2", "Role 2 Description", [null, "Role 2 Description Override"], [null, null]],
			["Role 3 Unique Text To Search For", "Role 3 Description", ["Role 3 Title Override", "Role 3 Description Override"], [null, "Role 3 Playlist Description Override"]],
		];
		
		DB::transaction(function() use (&$roles) {
			foreach($roles as $b=>$a) {
				$role = ProductionRole::firstOrCreate([
					"id"			=> $b+1,
					"position"		=> $b,
					"name"			=> $a[0],
					"description"	=> $a[1]
				]);
				$role->save();
				$role->id = $b+1; // fixes bug
				
				$mediaItemProductionRoleData = $a[2];
				$playlistProductionRoleData = $a[3];
				if (!is_null($mediaItemProductionRoleData)) {
					$roleMediaItem = $role->productionRoleMediaItem;
					if (is_null($roleMediaItem)) {
						$roleMediaItem = new ProductionRoleMediaItem();
					}
					$roleMediaItem->name_override = $mediaItemProductionRoleData[0];
					$roleMediaItem->description_override = $mediaItemProductionRoleData[1];
					$role->productionRoleMediaItem()->save($roleMediaItem);
				}
				else {
					// remove if exists
					$role->productionRoleMediaItem()->delete();
				}
				if (!is_null($playlistProductionRoleData)) {
					$rolePlaylist = $role->productionRolePlaylist;
					if (is_null($rolePlaylist)) {
						$rolePlaylist = new ProductionRolePlaylist();
					}
					$rolePlaylist->name_override = $playlistProductionRoleData[0];
					$rolePlaylist->description_override = $playlistProductionRoleData[1];
					$role->productionRolePlaylist()->save($rolePlaylist);
				}
				else {
					// remove if exists
					$role->productionRolePlaylist()->delete();
				}
			}
			ProductionRole::where("id", ">", count($roles))->delete();
		});
		$this->command->info('Production roles created/updated!');
	}


}
