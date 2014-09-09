<?php

use uk\co\la1tv\website\models\LiveStream;
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
					"server_address"	=>	"la1tv-wowza1.lancs.ac.uk:1935",
					"dvr_enabled"	=>	false,
					"stream_name"	=>	$a[1],
					"enabled"	=> false
				));
				$stream->save();
				$stream->qualities()->attach(QualityDefinition::find(1));
				$stream->qualities()->attach(QualityDefinition::find(2));
				
			});
		}
		
		$this->command->info('Live streams created and assigned qualities!');
	}


}
