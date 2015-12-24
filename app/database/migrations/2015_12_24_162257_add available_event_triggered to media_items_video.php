<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailableEventTriggeredToMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->boolean("available_event_triggered")->default(false);
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
			$table->dropColumn("available_event_triggered");
		});
	}

}
