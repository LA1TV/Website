<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingPushNotificationRegistrationEndpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('push_notification_registration_endpoints', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string("session_id", 255);
			$table->string('url');
			
			$table->foreign("session_id")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('cascade');
			
			$table->unique("session_id");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('push_notification_registration_endpoints');
	}

}
