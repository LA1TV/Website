<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlFromBridgeServiceToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->text("uri_from_dvr_bridge_service")->nullable();
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
			$table->dropColumn("uri_from_dvr_bridge_service");
		});
	}

}
