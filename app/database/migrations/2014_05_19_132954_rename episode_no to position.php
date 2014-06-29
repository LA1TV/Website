<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEpisodeNoToPosition extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_to_playlist', function(Blueprint $table)
		{
			$table->renameColumn('episode_no', 'position');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_to_playlist', function(Blueprint $table)
		{
			$table->renameColumn('position', 'episode_no');
		});
	}

}
