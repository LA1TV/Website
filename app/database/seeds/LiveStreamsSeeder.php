<?php

use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\LiveStreamQuality;
use uk\co\la1tv\website\models\QualityDefinition;

class LiveStreamsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		foreach(array(array("Studio Stream", "studio"), array("OB Stream 1", "ob1"), array("OB Stream 2", "ob2")) as $a) {
		
			DB::transaction(function() use(&$a) {
			
				$stream = new LiveStream(array(
					"name"		=>	$a[0],
					"description"	=>	NULL,
					"server_address"	=>	"la1tv-wowza1.lancs.ac.uk",
					"dvr_enabled"	=>	false,
					"stream_name"	=>	$a[1],
					"enabled"	=> false
				));
				$stream->save();
				
				$pos = 0;
				
				$quality = new LiveStreamQuality(array(
					"quality_id"	=> "auto",
					"position"		=> $pos++
				));
				$quality->liveStream()->associate($stream);
				$quality->qualityDefinition()->associate(QualityDefinition::find(7));
				$quality->save();
				
				$quality = new LiveStreamQuality(array(
					"quality_id"	=> "720p",
					"position"		=> $pos++
				));
				$quality->liveStream()->associate($stream);
				$quality->qualityDefinition()->associate(QualityDefinition::find(2));
				$quality->save();
				
				$quality = new LiveStreamQuality(array(
					"quality_id"	=> "360p",
					"position"		=> $pos++
				));
				$quality->liveStream()->associate($stream);
				$quality->qualityDefinition()->associate(QualityDefinition::find(4));
				$quality->save();
				
				$quality = new LiveStreamQuality(array(
					"quality_id"	=> "240p",
					"position"		=> $pos++
				));
				$quality->liveStream()->associate($stream);
				$quality->qualityDefinition()->associate(QualityDefinition::find(5));
				$quality->save();
				
				$quality = new LiveStreamQuality(array(
					"quality_id"	=> "160p",
					"position"		=> $pos++
				));
				$quality->liveStream()->associate($stream);
				$quality->qualityDefinition()->associate(QualityDefinition::find(6));
				$quality->save();
			});
		}
		
		$this->command->info('Live streams created and assigned qualities!');
	}


}
