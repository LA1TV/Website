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
			["Presenter", null, [null, null], [null, null]],
			["Camera Operator", null, [null, null], [null, null]],
			["Broadcast Vision Mixer", null, [null, null], [null, null]],
			["Projection Vision Mixer", null, [null, null], [null, null]],
			["Vision Mixer", null, [null, null], [null, null]],
			["Communications Manager", null, [null, null], [null, null]],
			["Autocue", null, [null, null], [null, null]],
			["Graphics Developer", null, [null, null], [null, null]],
			["Graphics Operator", null, [null, null], [null, null]],
			["VT Editor", null, [null, null], [null, null]],
			["VT Operator", null, [null, null], [null, null]],
			["Decoder Operator", null, [null, null], [null, null]],
			["Streaming Technician", null, [null, null], [null, null]],
			["Lighting", null, [null, null], [null, null]],
			["Broadcast Sound", null, [null, null], [null, null]],
			["House Sound", null, [null, null], [null, null]],
			["Sound", null, [null, null], [null, null]],
			["Floor Manager", null, [null, null], [null, null]],
			["Runner", null, [null, null], [null, null]],
			["Rigging Team", null, [null, null], [null, null]],
			["Scheduling", null, [null, null], [null, null]],
			["Social Media", null, [null, null], [null, null]],
			["Systems Developer", null, [null, null], [null, null]],
			["Guest Booking", null, [null, null], [null, null]],
			["Guest", null, [null, null], [null, null]],
			["Script Writer", null, [null, null], [null, null]],
			["Production Manager", null, [null, null], [null, null]],
			["Director", null, [null, null], [null, null]],
			["Assistant Director", null, [null, null], [null, null]],
			["Producer", null, [null, null], [null, null]],
			["Assistant Producer", null, [null, null], [null, null]],
		];
		
		DB::transaction(function() use (&$roles) {
			foreach($roles as $b=>$a) {
				$role = ProductionRole::find($b+1);
				if (is_null($role)) {
					$role = new ProductionRole();
				}
				$role->id = $b+1;
				$role->position = $b;
				$role->name = $a[0];
				$role->description = $a[1];
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
