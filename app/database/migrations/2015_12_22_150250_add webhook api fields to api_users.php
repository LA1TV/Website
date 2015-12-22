<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebhookApiFieldsToApiUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_users', function(Blueprint $table)
		{
			$table->boolean("can_use_webhooks")->default(false);
			$table->string("webhook_url")->nullable();
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
			$table->dropColumn("can_use_webhooks");
			$table->dropColumn("webhook_url");
		});
	}

}
