<?php

use uk\co\la1tv\website\models\QualityDefinition;

class QualityDefinitionsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$qualities = array("1080p", "720p", "640p", "360p", "240p");
		
		foreach($qualities as $b=>$a) {
			QualityDefinition::firstOrCreate(array("id"=>$b+1, "name"=>$a));
		}
		$this->command->info('Quality definitions created/updated!');
	}


}
