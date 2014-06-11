<?php

use uk\co\la1tv\website\models\FileExtension;

class FileExtensionsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$extensions = array("jpg", "jpeg", "png", "mp4");
		
		foreach($extensions as $b=>$a) {
			FileExtension::firstOrCreate(array("id"=>$b+1, "extension"=>$a));
		}
		$this->command->info('File extensions created/updated!');
	}


}
