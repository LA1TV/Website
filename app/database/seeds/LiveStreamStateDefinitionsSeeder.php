<?php

use uk\co\la1tv\website\models\LiveStreamStateDefinition;

class LiveStreamStateDefinitionsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		$states = array("Not Live", "Live", "Show Over");
		
		foreach($states as $b=>$a) {
			LiveStreamStateDefinition::firstOrCreate(array("id"=>$b+1, "name"=>$a));
		}
		$this->command->info('Live stream state definitions created/updated!');
	}


}
