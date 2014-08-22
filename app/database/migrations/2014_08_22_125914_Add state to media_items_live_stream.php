<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToMediaItemsLiveStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->integer("state_id")->unsigned()->default(1);
			
			$table->index("state_id");
			
			$table->foreign("state_id", "state_id_frn")->references('id')->on('live_stream_state_definitions')->onUpdate("restrict")->onDelete('restrict');

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
			$table->dropForeign("state_id_frn");
			$table->dropColumn("state");
		});
	}

}
