<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveStreams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('live_streams', function(Blueprint $table)
		{
			$table->increments('id');
			$table->String('name', 50);
			$table->text('description')->nullable();
			$table->String('load_balancer_server_address', 50)->nullable();
			$table->String('server_address', 50)->nullable();
			$table->boolean('dvr_enabled');
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
		Schema::drop('live_streams');
	}

}
