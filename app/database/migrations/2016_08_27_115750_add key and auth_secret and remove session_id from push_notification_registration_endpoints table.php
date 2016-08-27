<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyAndAuthSecretAndRemoveSessionIdFromPushNotificationRegistrationEndpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('push_notification_registration_endpoints')->truncate();
		Schema::table('push_notification_registration_endpoints', function(Blueprint $table)
		{
			$table->dropForeign("push_notification_registration_endpoints_session_id_foreign");
			$table->dropColumn("session_id");
			$table->string("key", 88);
			$table->string("auth_secret", 24);
			$table->integer("time_verified");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('push_notification_registration_endpoints')->truncate();
		Schema::table('push_notification_registration_endpoints', function(Blueprint $table)
		{
			$table->string("session_id", 255);
			$table->foreign("session_id")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('cascade');
			$table->dropColumn("key");
			$table->dropColumn("auth_secret");
			$table->dropColumn("time_verified");
		});
	}

}
