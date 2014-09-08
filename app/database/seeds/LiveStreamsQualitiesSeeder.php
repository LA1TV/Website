<?php

use uk\co\la1tv\website\models\LiveStreamQuality;
use uk\co\la1tv\website\models\QualityDefinition;

class LiveStreamsQualitiesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		rtmp://148.88.67.140:1935/live/studio_720p
		
		$qualities = array(
			array("rtmp://{domain}/{appName}/ngrp:{streamName}_all", 7, "rtmp/mp4"),
			array("rtmp://{domain}/{appName}/{streamName}_720", 2, "rtmp/mp4"),
			array("rtmp://{domain}/{appName}/{streamName}_360", 4, "rtmp/mp4"),
			array("rtmp://{domain}/{appName}/{streamName}_240", 5, "rtmp/mp4"),
			array("rtmp://{domain}/{appName}/{streamName}_160", 6, "rtmp/mp4")
		);
		
		foreach($qualities as $b=>$a) {
			$qualityDefinition = QualityDefinition::find($a[1]);
			$oldModel = LiveStreamQuality::find($b+1);
			if (!is_null($oldModel)) {
				$oldModel->delete();
			}
			$model = new LiveStreamQuality(array(
				"id"			=> $b+1,
				"position"		=> $b,
				"uri_template"	=> $a[0],
				"type"			=> $a[2]
			));
			$model->qualityDefinition()->associate($qualityDefinition);
			$model->save();
		}
		$this->command->info('Live stream qualities created/updated!');
	}

}
