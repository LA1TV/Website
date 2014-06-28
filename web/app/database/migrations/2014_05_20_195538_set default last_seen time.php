<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultLastSeenTime extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->dropColumn("last_seen");
		});
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->timestamp('last_seen')->default(DB::raw('CURRENT_TIMESTAMP'));
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
			$table->dropColumn("last_seen");
		});
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->timestamp('last_seen');
		});
	}

}
