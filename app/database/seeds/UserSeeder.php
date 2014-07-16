<?php

use uk\co\la1tv\website\models\User;
use uk\co\la1tv\website\models\PermissionGroup;

class UserSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		// this relies on the permission groups getting the same autoincremented ids each time which should happen
		
		
		DB::transaction(function() {
			$user = User::create(array(
				"cosign_user"	=>	"jenkinst",
				"admin"	=> true
			));
			PermissionGroup::find(6)->users()->attach($user);
		});
		
		DB::transaction(function() {
			$user = User::create(array(
				"cosign_user"	=>	"moscrop",
				"admin"	=> false
			));
			PermissionGroup::find(1)->users()->attach($user);
			PermissionGroup::find(2)->users()->attach($user);
		});
		
		DB::transaction(function() {
			$user = User::create(array(
				"cosign_user"	=>	"cosborn",
				"admin"	=> false
			));
			PermissionGroup::find(1)->users()->attach($user);
			PermissionGroup::find(2)->users()->attach($user);
			PermissionGroup::find(3)->users()->attach($user);
			PermissionGroup::find(4)->users()->attach($user);
		});
	
		if (App::environment() !== 'production' || $this->command->confirm('Do you want to create the user "test" with password "password" with admin permissions? [y|n]:', false)) {
			$user = User::create(array(
				"username"		=>	"test",
				"password_hash"	=>	Hash::make("password"),
				"admin"			=> true
			));
		}
		
		
		$this->command->info('CMS users created and attached to groups!');
	}


}
