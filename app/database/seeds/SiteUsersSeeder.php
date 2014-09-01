<?php

use uk\co\la1tv\website\models\SiteUser;

class SiteUsersSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$users = array(
			array("first_name"=>"Joe","last_name"=>"Bloggs","name"=>"Joe Bloggs"),
			array("first_name"=>"Tom","last_name"=>"Jenkinson","name"=>"Tom Jenkison"),
			array("first_name"=>"Chrisopher","last_name"=>"Osborn","name"=>"Chris Osborn"),
			array("first_name"=>"Luke","last_name"=>"Moscrop","name"=>"Luke Moscrop"),
			array("first_name"=>"Ben","last_name"=>"Freke","name"=>"Ben Freke")		
		);
		
		foreach($users as $b=>$a) {
			SiteUser::create(array_merge($a, array("fb_uid"=>$b)));
		}
		$this->command->info('Site users created!');
	}


}
