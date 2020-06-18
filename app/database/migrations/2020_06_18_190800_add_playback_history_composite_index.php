<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaybackHistoryCompositeIndex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playback_history', function(Blueprint $table)
		{
			$table->index(array('constitutes_view', 'media_item_id', 'type'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playback_history', function(Blueprint $table)
		{
			$table->dropIndex('playback_history_constitutes_view_media_item_id_type_index');
		});
	}

}
