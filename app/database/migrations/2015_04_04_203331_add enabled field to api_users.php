<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnabledFieldToApiUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_users', function(Blueprint $table)
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
		Schema::table('api_users', function(Blueprint $table)
		{
			$table->dropColumn("enabled");
		});
	}

}
