<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingDvrBridgeServiceUriToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->boolean("dvr_bridge_service_uri")->default(false);
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
			$table->dropColumn("dvr_bridge_service_uri");
		});
	}

}
