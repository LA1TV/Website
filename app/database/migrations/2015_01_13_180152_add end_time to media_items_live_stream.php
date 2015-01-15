<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndTimeToMediaItemsLiveStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->timestamp("end_time")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->dropColumn("end_time");
		});
	}

}
