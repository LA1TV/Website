<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSeriesIdToPlaylistId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_to_playlist', function(Blueprint $table)
		{
			$table->renameColumn('series_id', 'playlist_id');
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
			$table->renameColumn('playlist_id', 'series_id');
		});
	}

}
