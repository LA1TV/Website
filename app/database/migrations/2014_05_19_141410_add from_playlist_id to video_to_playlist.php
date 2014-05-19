<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromPlaylistIdToVideoToPlaylist extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_to_playlist', function(Blueprint $table)
		{
			$table->integer('from_playlist_id')->unsigned()->nullable();
			
			$table->index("from_playlist_id");
			
			$table->foreign("from_playlist_id")->references('id')->on('playlists')->onUpdate("restrict")->onDelete('cascade');

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
			$table->dropColumn("from_playlist_id");
		});
	}

}
