<?php

use uk\co\la1tv\website\models\LiveStreamQuality;

class TruncateLiveStreamQualities extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$qualities = LiveStreamQuality::all();
		foreach($qualities as $a) {
			$a->delete();
		}
		$this->command->info('Live stream qualities truncated!');
	}


}
