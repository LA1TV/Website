<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFbLastUpdateTimeToSiteUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->timestamp('fb_last_update_time');
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
			$table->dropColumn('fb_last_update_time');
		});
	}

}
