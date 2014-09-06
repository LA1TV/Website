<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNameAndDescriptionFromMediaItemsLiveStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->dropColumn("name");
			$table->dropColumn("description");
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
			$table->string("name", 50);
			$table->text("description");
		});
	}

}
