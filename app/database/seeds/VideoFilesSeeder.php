<?php

use uk\co\la1tv\website\models\VideoFile;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\QualityDefinition;
use uk\co\la1tv\website\models\VodVideoGroup;
use uk\co\la1tv\website\models\File;

class VideoFilesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		$group = VodVideoGroup::first();
		$destinationFile = File::first();
		
		$file = new VideoFile(array(
			"width"		=>	1920,
			"height"	=>	1080
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(1));
		$file->vodVideoGroup()->associate($group);
		$file->file()->associate($destinationFile);
		$file->save();
		
		$file = new VideoFile(array(
			"width"		=>	1280,
			"height"	=>	720
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(2));
		$file->vodVideoGroup()->associate($group);
		$file->file()->associate($destinationFile);
		$file->save();
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	480
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(3));
		$file->vodVideoGroup()->associate($group);
		$file->file()->associate($destinationFile);
		$file->save();
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	360
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(4));
		$file->vodVideoGroup()->associate($group);
		$file->file()->associate($destinationFile);
		$file->save();
		
		$file = new VideoFile(array(
			"width"		=>	426,
			"height"	=>	240
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(5));
		$file->vodVideoGroup()->associate($group);
		$file->file()->associate($destinationFile);
		$file->save();
		
		$this->command->info('Video files records created!');
	}


}
