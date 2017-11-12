<?php

use uk\co\la1tv\website\models\QualityDefinition;

class QualityDefinitionsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$qualities = array(
			// name, position
			array("1080p", 2),
			array("720p", 3),
			array("480p", 4),
			array("360p", 5),
			array("240p", 6),
			array("160p", 7),
			array("Auto", 8),
			array("Native", 9),
			array("1440p", 0),
			array("4k", 1)
		);
		
		foreach($qualities as $b=>$a) {
			QualityDefinition::firstOrCreate(array("id"=>$b+1, "name"=>$a[0], "position"=>$a[1]));
		}
		$this->command->info('Quality definitions created/updated!');
	}


}
