<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string("owner", 50)->unique();
			$table->text("information")->nullable();
			$table->string("key", 40)->unique();
			$table->boolean("can_view_vod_uris")->default(false);
			$table->boolean("can_view_stream_uris")->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('api_users');
	}

}
