<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLiveStreamIdFromLiveStreamsQualities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->dropForeign("live_streams_qualities_live_stream_id_foreign");
			$table->dropColumn("live_stream_id");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->integer("live_stream_id")->unsigned();
			$table->index("live_stream_id");
			$table->foreign("live_stream_id")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

}
