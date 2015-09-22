<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovingPlaybackTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('playback_times');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('playback_times', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("user_id")->unsigned();
			$table->integer("file_id")->unsigned();
			$table->integer("time")->unsigned();
			$table->timestamps();
			
			$table->foreign("user_id", "playback_times_user_id_foreign")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("file_id", "playback_times_file_id_foreign")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

}
