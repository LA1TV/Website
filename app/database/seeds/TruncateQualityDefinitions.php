<?php

use uk\co\la1tv\website\models\QualityDefinition;

class TruncateQualityDefinitions extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$qualities = QualityDefinition::all();
		foreach($qualities as $a) {
			$a->delete();
		}
		$this->command->info('Quality definitions truncated!');
	}


}
