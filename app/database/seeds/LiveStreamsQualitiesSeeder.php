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
		
		$qualities = array(
			array("http://{domain}:1935/{appName}/{streamName}_all", 7),
			array("http://{domain}:1935/{appName}/ngrp:{streamName}_720", 2),
			array("http://{domain}:1935/{appName}/{streamName}_360", 4),
			array("http://{domain}:1935/{appName}/{streamName}_240", 5),
			array("http://{domain}:1935/{appName}/{streamName}_160", 6)
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
				"uri_template"	=> $a[0]
			));
			$model->qualityDefinition()->associate($qualityDefinition);
			$model->save();
		}
		$this->command->info('Live stream qualities created/updated!');
	}

}
