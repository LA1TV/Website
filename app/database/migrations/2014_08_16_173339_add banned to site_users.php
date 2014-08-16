<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBannedToSiteUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->boolean("banned")->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->dropColumn("banned");
		});
	}

}
