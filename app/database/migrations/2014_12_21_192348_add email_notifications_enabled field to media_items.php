<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailNotificationsEnabledFieldToMediaItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->boolean("email_notifications_enabled")->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->dropColumn("email_notifications_enabled");
		});
	}

}
