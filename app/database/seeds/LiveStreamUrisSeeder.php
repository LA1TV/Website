<?php

use uk\co\la1tv\website\models\LiveStreamUri;
use uk\co\la1tv\website\models\QualityDefinition;

class LiveStreamUrisSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		$qualities = array(
			array("rtmp://{domain}/{appName}/{streamName}_720p", 2, "rtmp/mp4", null),
			array("http://{domain}/{appName}/{streamName}_720p/playlist.m3u8", 2, "video/mp4", "mobile"),
			array("rtmp://{domain}/{appName}/{streamName}_360p", 4, "rtmp/mp4", null),
			array("http://{domain}/{appName}/{streamName}_360p/playlist.m3u8", 4, "video/mp4", "mobile"),
			array("rtmp://{domain}/{appName}/{streamName}_240p", 5, "rtmp/mp4", null),
			array("http://{domain}/{appName}/{streamName}_240p/playlist/playlist.m3u8", 5, "video/mp4", "mobile"),
			array("rtmp://{domain}/{appName}/{streamName}_160p", 6, "rtmp/mp4", null),
			array("http://{domain}/{appName}/{streamName}_160p/playlist.m3u8", 6, "video/mp4", "mobile"),
			array("rtmp://{domain}/{appName}/ngrp:{streamName}_all", 7, "rtmp/mp4", null),
			array("http://{domain}/{appName}/{streamName}/playlist.m3u8", 7, "video/mp4", "mobile")
		);
		
		foreach($qualities as $b=>$a) {
			$qualityDefinition = QualityDefinition::find($a[1]);
			$oldModel = LiveStreamUri::find($b+1);
			if (!is_null($oldModel)) {
				$oldModel->delete();
			}
			$model = new LiveStreamUri(array(
				"position"		=> $b,
				"uri_template"	=> $a[0],
				"type"			=> $a[2],
				"supported_devices"	=> $a[3]
			));
			$model->qualityDefinition()->associate($qualityDefinition);
			$model->save();
		}
		$this->command->info('Live stream uris created/updated!');
	}

}
