<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLiveStreamIdToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->integer("live_stream_id")->unsigned();
			$table->index("live_stream_id");
			$table->foreign("live_stream_id", "live_stream_uris_live_stream_id_foreign")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->dropForeign("live_stream_uris_live_stream_id_foreign");
			$table->dropColumn("live_stream_id");
		});
	}

}
