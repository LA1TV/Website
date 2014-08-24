<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveIsLiveRecordingFromMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->dropColumn("is_live_recording");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->boolean("is_live_recording")->default(false);
		});
	}

}
