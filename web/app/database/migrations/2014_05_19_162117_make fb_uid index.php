<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFbUidIndex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->unique("fb_uid");
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
			$table->dropIndex("site_users_fb_uid_unique");
		});
	}

}
