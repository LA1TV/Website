<?php

use uk\co\la1tv\website\models\VideoFile;
use uk\co\la1tv\website\models\MediaItemVideo;

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
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	1280,
			"height"	=>	720
		));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	480
		));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	640,
			"height"	=>	360
		));
		$mediaItemVideo->videoFiles()->save($file);
		
		$file = new VideoFile(array(
			"width"		=>	426,
			"height"	=>	240
		));
		$mediaItemVideo->videoFiles()->save($file);
		
		$this->command->info('Video files records created and assigned to video media items!');
	}


}
