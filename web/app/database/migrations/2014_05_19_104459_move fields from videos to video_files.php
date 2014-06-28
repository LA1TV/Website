<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveFieldsFromVideosToVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->dropColumn("is_live_recording");
			$table->dropColumn("time_recorded");
			$table->dropColumn("scheduled_publish_time");
		});
		
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->boolean('is_live_recording');
			$table->timestamp('time_recorded')->nullable();
			$table->timestamp('scheduled_publish_time')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->dropColumn("is_live_recording");
			$table->dropColumn("time_recorded");
			$table->dropColumn("scheduled_publish_time");
		});
		
		Schema::table('videos', function(Blueprint $table)
		{
			$table->boolean('is_live_recording');
			$table->timestamp('time_recorded')->nullable();
			$table->timestamp('scheduled_publish_time')->nullable();
		});
	}

}
