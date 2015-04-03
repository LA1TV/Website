<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNoLongerNeededFieldsFromLiveStreams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_streams', function(Blueprint $table)
		{
			$table->dropColumn("load_balancer_server_address");
			$table->dropColumn("server_address");
			$table->dropColumn("dvr_enabled");
			$table->dropColumn("stream_name");
			$table->dropColumn("app_name");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_streams', function(Blueprint $table)
		{
			$table->string("load_balancer_server_address", 50)->nullable();
			$table->string("server_address", 50)->nullable();
			$table->boolean("dvr_enabled");
			$table->string("stream_name", 50);
			$table->string("app_name", 50);
		});
	}

}
