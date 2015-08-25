<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingPlayingToWatchingNow extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('watching_now', function(Blueprint $table)
		{
			$table->boolean("playing")->default(true);
			$table->timestamp("last_play_time")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('watching_now', function(Blueprint $table)
		{
			$table->dropColumn("playing");
			$table->dropColumn("last_play_time");
		});
	}

}
