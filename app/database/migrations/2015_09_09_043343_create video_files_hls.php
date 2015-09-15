<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoFilesHls extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_files_hls', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("video_files_id")->unsigned();
			$table->integer("playlist_file_id")->unsigned();
			$table->integer("segment_file_id")->unsigned();
			$table->timestamps();

			$table->index("video_files_id");
			$table->index("playlist_file_id");
			$table->index("segment_file_id");
			
			$table->foreign("video_files_id")->references('id')->on('video_files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("playlist_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("segment_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_files_hls');
	}

}
