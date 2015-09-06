<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasDvrToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->boolean("has_dvr")->nullable();
		});
		$this->setDefaultValues();
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
			$table->dropColumn("has_dvr");
		});
	}

	private function setDefaultValues() {
		DB::table('live_stream_uris')->where('dvr_bridge_service_uri', false)->update(["has_dvr" => false]);
	}

}
