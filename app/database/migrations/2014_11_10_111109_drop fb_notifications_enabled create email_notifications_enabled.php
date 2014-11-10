<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFbNotificationsEnabledCreateEmailNotificationsEnabled extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->boolean("email_notifications_enabled")->default(true);
			$table->dropColumn("fb_notifications_enabled");
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
			$table->dropColumn("email_notifications_enabled");
			$table->boolean("fb_notifications_enabled")->default(true);
		});
	}

}
