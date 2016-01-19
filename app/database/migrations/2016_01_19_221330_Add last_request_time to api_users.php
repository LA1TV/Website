<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastRequestTimeToApiUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_users', function(Blueprint $table)
		{
			$table->timestamp("last_request_time")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('api_users', function(Blueprint $table)
		{
			$table->dropColumn("last_request_time");
		});
	}

}
