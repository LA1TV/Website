<?php

use uk\co\la1tv\website\models\ProductionRole;

class ProductionRolesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$roles = [
			["name", "description"]
		];
		
		foreach($roles as $b=>$a) {
			$role = new ProductionRole([
				"id"			=> $b+1,
				"name"			=> $a[0],
				"description"	=> $a[1]
			]);
			$role->save();
		}
		$this->command->info('Production roles created/updated!');
	}


}
