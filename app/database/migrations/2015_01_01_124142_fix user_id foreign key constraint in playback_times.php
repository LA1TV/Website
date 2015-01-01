<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUserIdForeignKeyConstraintInPlaybackTimes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playback_times', function(Blueprint $table)
		{
			$table->dropForeign("playback_times_user_id_foreign");
			DB::table('playback_times')->truncate();
			$table->foreign("user_id", "playback_times_user_id_foreign")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playback_times', function(Blueprint $table)
		{
			$table->dropForeign("playback_times_user_id_foreign");
			DB::table('playback_times')->truncate();
			$table->foreign("user_id", "playback_times_user_id_foreign")->references('id')->on('users')->onUpdate("restrict")->onDelete('cascade');
		});
	}

}
