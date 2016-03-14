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
			// NEVER CHANGE THE ID's ONCE THEY HAVE BEEN USED
			// id, name, description, valid for media item ([name override, description override]), valid for playlist ([name override, description override])
			[1, "Presenter", null, [null, null], [null, null]],
			[2, "Actor", null, [null, null], [null, null]],
			[3, "Puppeteer", null, [null, null], [null, null]],
			[4, "Camera Operator", null, [null, null], [null, null]],
			[5, "Boom Operator", null, [null, null], [null, null]],
			[6, "Broadcast Vision Mixer", null, [null, null], [null, null]],
			[7, "Projection Vision Mixer", null, [null, null], [null, null]],
			[8, "Communications Manager", null, [null, null], [null, null]],
			[9, "Autocue", null, [null, null], [null, null]],
			[10, "Graphics Developer", null, [null, null], [null, null]],
			[11, "Graphics Operator", null, [null, null], [null, null]],
			[12, "VT Editor", null, [null, null], [null, null]],
			[13, "VT Operator", null, [null, null], [null, null]],
			[14, "Decoder Operator", null, [null, null], [null, null]],
			[15, "Streaming Technician", null, [null, null], [null, null]],
			[16, "Lighting", null, [null, null], [null, null]],
			[17, "Broadcast Sound", null, [null, null], [null, null]],
			[18, "House Sound", null, [null, null], [null, null]],
			[20, "Make-up", null, [null, null], [null, null]],
			[21, "Costume Designer", null, [null, null], [null, null]],
			[22, "Floor Manager", null, [null, null], [null, null]],
			[23, "Runner", null, [null, null], [null, null]],
			[24, "Agent", null, [null, null], [null, null]],
			[25, "Rigging Team", null, [null, null], [null, null]],
			[26, "Scheduling", null, [null, null], [null, null]],
			[27, "Social Media", null, [null, null], [null, null]],
			[28, "Systems Developer", null, [null, null], [null, null]],
			[29, "Guest Booking", null, [null, null], [null, null]],
			[30, "Guest", null, [null, null], [null, null]],
			[31, "Location Manager", null, [null, null], [null, null]],
			[32, "Driver", null, [null, null], [null, null]],
			[33, "Script Writer", null, [null, null], [null, null]],
			[34, "Production Manager", null, [null, null], [null, null]],
			[35, "Choreographer", null, [null, null], ["Series Choreographer", null]],
			[36, "Assistant Choreographer", null, [null, null], ["Assistant Series Choreographer", null]],
			[37, "Art Director", null, [null, null], ["Series Art Director", null]],
			[38, "Assistant Art Director", null, [null, null], ["Series Assistant Art Director", null]],
			[39, "Editor", null, ["Programme Editor", null], ["Series Editor", null]],
			[40, "Director", null, [null, null], ["Series Director", null]],
			[41, "Assistant Director", null, [null, null], ["Series Assistant Director", null]],
			[42, "Producer", null, [null, null], ["Series Producer", null]],
			[43, "Assistant Producer", null, [null, null], ["Series Assistant Producer", null]],
		];
		
		DB::transaction(function() use (&$roles) {
			// increase all the positions past the maximum one
			// otherwise there will be integrity issues when they are changed below
			// because they have to be unique
			foreach (ProductionRole::get() as $b=>$role) {
				$count = ProductionRole::count();
				$numRoles = count($roles);
				if ($numRoles > $count) {
					$count = $numRoles;
				}
				$role->position = $count+$b;
				$role->save();
			}

			foreach($roles as $b=>$a) {
				$role = ProductionRole::find($a[0]);
				if (is_null($role)) {
					$role = new ProductionRole();
				}
				$role->id = $a[0];
				$role->position = $b;
				$role->name = $a[1];
				$role->description = $a[2];
				$role->save();
				$role->id = $a[0]; // fixes bug
				
				$mediaItemProductionRoleData = $a[3];
				$playlistProductionRoleData = $a[4];
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
			ProductionRole::where("position", ">", count($roles)-1)->delete();
		});
		$this->command->info('Production roles created/updated!');
	}


}
