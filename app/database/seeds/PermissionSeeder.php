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
			array("first_name"=>"Joe","last_name"=>"Bloggs","name"=>"Joe Bloggs","email"=>"j.bloggs@outlook.com"),
			array("first_name"=>"Tom","last_name"=>"Jenkinson","name"=>"Tom Jenkison","email"=>"t.jenkinson@lancaster.ac.uk"),
			array("first_name"=>"Chrisopher","last_name"=>"Osborn","name"=>"Chris Osborn","email"=>"c.osborn@la1tv.co.uk"),
			array("first_name"=>"Luke","last_name"=>"Moscrop","name"=>"Luke Moscrop","email"=>"l.moscrop@la1tv.co.uk"),
			array("first_name"=>"Ben","last_name"=>"Freke","name"=>"Ben Freke","email"=>"b.freke@la1tv.co.uk")		
		);
		
		foreach($users as $b=>$a) {
			with(new SiteUser(array_merge($a, array("fb_uid"=>$b))))->save();
		}
		$this->command->info('Site users created!');
	}


}
