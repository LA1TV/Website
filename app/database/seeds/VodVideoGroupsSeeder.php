<?php

use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\VodVideoGroup;

class VodVideoGroupsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		$group = new VodVideoGroup(array());
		$group->sourceFile()->associate(File::where("in_use", "=", true)->first());
		$group->save();
		$this->command->info('VOD Video Groups created!');
	}


}
