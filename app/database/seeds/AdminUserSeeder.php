<?php

use uk\co\la1tv\website\models\User;

class AdminUserSeeder extends Seeder {

	/**
	 * Use this to create the initial admin account.
	 *
	 * @return void
	 */
	public function run() {
		
		if (App::environment() == "production") {
			$this->command->error("You are about to create a user with admin permissions?");
			if (!$this->command->confirm('Are you sure you want to continue? [y|n]:', false))
			{
				$this->command->comment("Aborting.");
				return;
			}
		}
		
		$username = $this->command->ask('Enter a username [admin]:', "admin");
		if (trim($username) === "") {
			$this->command->error("Invalid username.");
			return;
		}
		
		$this->command->comment("Creating user: " . $username);
		
		$user = User::where("username", $username)->first();
		if (!is_null($user)) {
			
			if (!$this->command->confirm("A user with that username already exists. This user will be removed. Are you sure you want to continue? [y|n]:", false)) {
				$this->command->comment("Aborting.");
				return;
			}
			$user->delete();
		}
		
		$password = $this->command->secret('Enter a password:');
		if (is_null($password) || $password === "") {
			$this->command->error("A password is required.");
			$this->command->comment("Aborting.");
			return;
		}
		$passwordConf = $this->command->secret('Re-enter password:');
		
		if ($password !== $passwordConf) {
			$this->command->error("Passwords did not match.");
			$this->command->comment("Aborting.");
			return;
		}
		
		User::create(array(
			"username"		=> $username,
			"password_hash"	=>	Hash::make($password),
			"disabled"		=> false,
			"admin"			=> true
		));
	
		$this->command->comment("Created user '" . $username . "' with admin permissions.");
	}

}
