<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccessTokenToSiteUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->text("fb_access_token");
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
			$table->dropColumn("fb_access_token");
		});
	}

}
