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
			$table->dropForeign('video_to_series_video_id_foreign');
			$table->dropForeign('video_to_series_series_id_foreign');
			
			$table->renameColumn('series_id', 'playlist_id');
			
			$table->foreign("video_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("series_id")->references('id')->on('series')->onUpdate("restrict")->onDelete('cascade');
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
			$table->dropForeign('video_to_playlist_video_id_foreign');
			$table->dropForeign('video_to_playlist_series_id_foreign');
			
			$table->renameColumn('playlist_id', 'series_id');
			
			$table->foreign("video_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("series_id")->references('id')->on('series')->onUpdate("restrict")->onDelete('cascade');
		});
	}

}
