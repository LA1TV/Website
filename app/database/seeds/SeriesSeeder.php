<?php

use uk\co\la1tv\website\models\Series;

class SeriesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		Series::create(array(
			"name"	=> "Roses",
			"description"	=> "The yearly Roses sporting competition between Lancaster and York.",
			"enabled"	=> true
		));
		Series::create(array(
			"name"	=> "LUSU Hustings",
			"description"	=> null,
			"enabled"	=> false
		));
		$this->command->info('Series created!');
	}

}
