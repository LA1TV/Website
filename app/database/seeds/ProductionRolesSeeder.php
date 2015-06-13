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
			
		];
		
		foreach($roles as $b=>$a) {
		//	QualityDefinition::firstOrCreate(array("id"=>$b+1, "name"=>$a, "position"=>$b));
		}
		$this->command->info('Production roles created/updated!');
	}


}
