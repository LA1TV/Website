<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingEnabledFieldToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->boolean("enabled")->default(true);
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
			$table->dropColumn("enabled");
		});
	}

}
