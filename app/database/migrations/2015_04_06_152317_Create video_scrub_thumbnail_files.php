<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoScrubThumbnailFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_scrub_thumbnail_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("file_id")->unsigned();
			$table->integer("video_file_id")->unsigned();
			$table->integer("time")->unsigned();
			$table->timestamps();
			
			$table->index("file_id");
			$table->index("video_file_id");
			
			$table->foreign("file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("video_file_id")->references('id')->on('video_files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_scrub_thumbnail_files');
	}

}
