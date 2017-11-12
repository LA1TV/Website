<?php

use uk\co\la1tv\website\models\QualityDefinition;

class QualityDefinitionsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$qualities = array("4k", "1440p", "1080p", "720p", "480p", "360p", "240p", "160p", "Auto", "Native");
		
		foreach($qualities as $b=>$a) {
			QualityDefinition::firstOrCreate(array("id"=>$b+1, "name"=>$a, "position"=>$b));
		}
		$this->command->info('Quality definitions created/updated!');
	}


}
