<?php

use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\FileType;
use uk\co\la1tv\website\models\UploadPoint;

class FileSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		// make sure there is at least one file entry that other seeders can use
		
		if (File::where("in_use", "=", true)->count() === 0) {
			$file = new File(array(
				"in_use"=>true,
				"size"=>rand(10, 9999)
			));
			$file->fileType()->associate(FileType::first());
			$file->uploadPoint()->associate(UploadPoint::first());
			$file->save();
		}
		
		$this->command->info('File record created.');
	}


}
