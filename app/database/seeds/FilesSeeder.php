<?php

use uk\co\la1tv\website\models\File;

class FilesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		File::create(array("in_use"=>true)); // breakfast show cover
		File::create(array("in_use"=>true)); // breakfast show banner
		File::create(array("in_use"=>true)); // afternoon show cover
		$this->command->info('File records created!');
	}


}
