<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoFilesDash extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_files_dash', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("video_files_id")->unsigned();
			$table->integer("media_presentation_description_file_id")->unsigned();
			$table->integer("audio_channel_file_id")->unsigned();
			$table->integer("video_channel_file_id")->unsigned();
			$table->timestamps();

			$table->index("video_files_id");
			$table->index("media_presentation_description_file_id", "dash_mpd_file_id");
			$table->index("audio_channel_file_id");
			$table->index("video_channel_file_id");
			
			$table->foreign("video_files_id")->references('id')->on('video_files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("media_presentation_description_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("audio_channel_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("video_channel_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_files_dash');
	}

}
