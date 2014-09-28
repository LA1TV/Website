<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_sessions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string("session_id", 255)->unique();
			$table->integer("user_id")->unsigned();
			$table->timestamps();
			
			$table->index("user_id");
			
			$table->foreign("session_id", "user_session_id_frn")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("user_id", "user_sessions_user_id_foreign")->references('id')->on('users')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_sessions');
	}

}
