<?php

use uk\co\la1tv\website\models\VideoFile;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\QualityDefinition;

class VideoFilesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		// presumes that mediaitemvideo's have just been created with autoincrementing id
		$mediaItemVideo = MediaItemVideo::find(1);
		
		$file = new VideoFile(array(
			"width"		=>	1920,
			"height"	=>	1080
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(1));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	1280,
			"height"	=>	720
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(2));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	480
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(3));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	360
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(4));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	426,
			"height"	=>	240
		));
		$file->qualityDefinition()->associate(QualityDefinition::find(5));
		$mediaItemVideo->videoFiles()->save($file);
		
		$this->command->info('Video files records created and assigned to video media items!');
	}


}
