<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameVideoIdToMediaItemId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->renameColumn('video_id', 'media_item_id');
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
			$table->renameColumn('media_item_id', 'video_id');
		});
	}

}
