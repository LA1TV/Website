<?php

use uk\co\la1tv\website\models\Show;

class ShowsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		Show::create(array(
			"name"	=> "Roses",
			"description"	=> "The yearly Roses sporting competition between Lancaster and York.",
			"enabled"	=> true
		));
		Show::create(array(
			"name"	=> "LUSU Hustings",
			"description"	=> null,
			"enabled"	=> false
		));
		$this->command->info('Shows created!');
	}

}
