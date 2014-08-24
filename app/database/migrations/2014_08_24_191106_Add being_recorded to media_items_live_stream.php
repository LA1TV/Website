<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBeingRecordedToMediaItemsLiveStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->boolean("being_recorded")->default(false);
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
			$table->dropColumn("being_recorded");
		});
	}

}
